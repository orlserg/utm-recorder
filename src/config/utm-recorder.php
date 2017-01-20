<?php

return [

    /* ------------------------------------------------------------------------------------------------
     |  Table name
     | ------------------------------------------------------------------------------------------------
     |  Define table name which will be linked with user visits.
     */
    'link_visits_with' => 'visitors',

    'link_visits_with_model' => \App\Visitor::class,

    'cache_utm' => true,

    'cache_time' => 256,

    'session_key' => 'utm-recorder',

    'capture_internal' => true,

    /* ------------------------------------------------------------------------------------------------
     |  Allowed methods
     | ------------------------------------------------------------------------------------------------
     |  Methods which will be recorded.
     */
    'allowed_methods' => [
        'GET'
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Host black list
     | ------------------------------------------------------------------------------------------------
     |  Visits from this hosts will not be recorded.
     */
    'host_blacklist' => [
        'google.ru'
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Allowed sources
     | ------------------------------------------------------------------------------------------------
     |  Only visits from this sources (UTM) will be recorded.
     */
    'allowed_sources' => [
        'leadssu'
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Disallowed sources
     | ------------------------------------------------------------------------------------------------
     |  Visits from this sources (UTM) will not be recorded.
     */
    'disallowed_sources' => [
        'goroten'
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Attributes
     | ------------------------------------------------------------------------------------------------
     |  List of parameters which will be recorded
     */
    'record_attributes' => [
        'utm_source',
        'utm_campaign',
        'utm_medium',
        'utm_term',
        'utm_content',
        'wmid',
        'tid'
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Transform attributes
     | ------------------------------------------------------------------------------------------------
     |  Listed attribute names will be converted to your own
     */
    'transform_attributes' => [
        // 'tid' => 'utm_tid'
    ],

];
