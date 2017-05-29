<?php

namespace Orlserg\UtmRecorder\Middleware;

use Illuminate\Http\Request;
use Closure;
use Orlserg\UtmRecorder\Models\Visit;

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

    protected $is_internal;

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

        if (!$this->isAllowedMethod()) {
            return $this->response;
        }

        if ($this->is_internal = $this->isInternalReferrer()) {
            if (!config('utm-recorder.capture_internal')) {
                return $this->response;
            }
        }

        if ($this->isHostBlacklisted()) {
            return $this->response;
        }

        if (!$this->checkSource()) {
            return $this->response;
        }

        $visit = $this->createVisit();
        $this->trackVisit($visit);

        return $this->response;
    }

    protected function isAllowedMethod()
    {
        return in_array($this->request->getMethod(), config('utm-recorder.allowed_methods'));
    }

    /**
     * Return true if source is allowed
     *
     * @param $source
     * @return bool
     */
    protected function isAllowedSource($source)
    {
        $allowed_sources = config('utm-recorder.allowed_sources');
        if (!$allowed_sources) {
            return true;
        }

        return in_array($source, $allowed_sources);
    }

    /**
     * Return true if source is disallowed
     *
     * @param $source
     * @return bool
     */
    protected function isDisallowedSource($source)
    {
        $disallowed_sources = config('utm-recorder.disallowed_sources');
        if (!$disallowed_sources) {
            return false;
        }

        return in_array($source, $disallowed_sources);
    }

    /**
     * @return bool
     */
    protected function checkSource()
    {
        $source = $this->request->has('utm_source')
            ? $this->request->input('utm_source')
            : null;

        if ($source) {
            return $this->isAllowedSource($source) && !$this->isDisallowedSource($source);
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isInternalReferrer()
    {
        $referrer_domain = parse_url($this->request->headers->get('referer'));
        $referrer_domain = isset($referrer_domain['host']) ? $referrer_domain['host'] : null;
        $request_domain = $this->request->server('SERVER_NAME');

        if (empty($referrer_domain) || $referrer_domain == $request_domain) {
            return true;
        }

        return false;
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
            if (!empty($referrer_domain) && $referrer_domain == $host) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function captureUTM()
    {
        $utm = [];
        $parameters = config('utm-recorder.record_attributes');
        $transformable = config('utm-recorder.transform_attributes');

        foreach ($parameters as $parameter) {
            if ($this->request->has($parameter)) {
                if ($transformable) {
                    foreach ($transformable as $old => $new) {
                        $utm[$new] = !$this->request->has($old) ?: $this->request->input($old);
                    }
                } else {
                    $utm[$parameter] = $this->request->input($parameter);
                }
            }
        }

        return collect($utm);
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
        if (\Auth::check()) {
            \Auth::user()->visits()->save($visit);
            $data = $visit->getUtms();
            $visit->params()->sync($data);
        } else {
            session()->push(config('utm-recorder.session_key'), $visit);
        }
    }

    protected function createVisit()
    {
        $visit = new Visit(
            array_merge($this->captureReferer(), [
                'method' => $this->request->getMethod(),
                'is_internal' => $this->is_internal
            ])
        );

        $visit->setUtms($this->captureUTM());

        return $visit;
    }
}
