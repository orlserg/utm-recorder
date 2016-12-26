<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Closure;

class UtmRecorder
{
    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Response
     */
    protected $response;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->request = $request;
        $this->response = $next($this->request);

        if (!$this->request->isMethod('get')) {
            return $this->response;
        }

        if ($this->isHostBlacklisted()) {
            return $this->response;
        }

        if ($this->isInternalReferrer()) {
            return $this->response;
        }

        if ($this->isAllowedSource()) {
            return $this->response;
        }

        $this->trackVisit($this->captureUTM());
        return $this->response;
    }

    protected function getSessionKey()
    {
        return config('utm-recorder.session_key');
    }

    /**
     * @return bool
     */
    protected function isAllowedSource()
    {
        $source = $this->request->has('utm_source')
            ? $this->request->input('utm_source')
            : null;

        if (!$source) {
            return false;
        }

        $allowed_sources = config('utm-recorder.allowed_sources');
        if (!$allowed_sources) {
            return true;
        }

        foreach ($allowed_sources as $allowed_source) {
            if ($allowed_source != $source) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isInternalReferrer()
    {
        if (config('utm-recorder.disable_internal_links')) {
            return false;
        }

        $referrer_domain = parse_url($this->request->headers->get('referer'));
        $referrer_domain = !isset($referrer_domain['host']) ? null : $referrer_domain['host'];
        $request_domain = $this->request->server('SERVER_NAME');
        if (!empty($referrer_domain) && $referrer_domain == $request_domain) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isHostBlacklisted()
    {
        $blacklist = config('utm-recorder.host_blacklist');
        if (!$blacklist) {
            return false;
        }

        $referrer_domain = parse_url($this->request->headers->get('referer'));
        $referrer_domain = isset($referrer_domain['host']) ? $referrer_domain['host'] : null;

        foreach ($blacklist as $host) {
            if (!empty($referrer_domain) == $host) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    protected function captureUTM()
    {
        $parameters = config('utm-recorder.record_attributes');
        $transformable = config('utm-recorder.transform_attributes');

        $utm = [];
        foreach ($parameters as $parameter) {
            if ($this->request->has($parameter)) {
                if ($transformable) {
                    foreach ($transformable as $old => $new) {
                        if ($this->request->has($old)) {
                            $utm[$new] = $this->request->input($old);
                        } else {
                            $utm[$new] = null;
                        }
                    }
                } else {
                    $utm[$parameter] = $this->request->input($parameter);
                }
            } else {
                $utm[$parameter] = null;
            }
        }

        return $utm;
    }

    /**
     * @return array
     */
    protected function captureReferer()
    {
        $referrer = [];
        $referrer['referrer_url'] = $this->request->headers->get('referer');
        $parsedUrl = parse_url($referrer['referrer_url']);
        $referrer['referrer_domain'] = isset($parsedUrl['host']) ? $parsedUrl['host'] : null;
        return $referrer;
    }

    /**
     * @param Visit $visit
     * @internal param Visit $data
     */
    protected function trackVisit(Visit $visit)
    {
        $key = $this->getSessionKey();
        $stored = session($key);
        $stored[] = $visit;
        session($key, $stored);
    }
}
