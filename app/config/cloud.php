<?php
return array(
    'enabled' => false, //Enables/Disables Google Cloud Storage functions

    'key' => '',        //Path to Google Cloud Key file
    'project' => '',    //Project name in Google Cloud
    'streaming_bucket' => '',   //Bucket name of streaming bucket

    'secure_url_duration' => 1800,   //Bucket name of streaming bucket
    'secure_url_origin' => 'http://newawards.oxobox.tv',   //Bucket name of streaming bucket

    'default_storage_sources_bucket' => '',   //Default Google Cloud Storage bucket for new contests

    'manager' => '',   //Absolute path to Cloud Manager python
    'instances_zone' => []
);