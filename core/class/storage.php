<?php

/** This file is part of Landlord Web Solutions fork of KCFinder project
 *
 *      @desc File storage abstraction
 *   @package KCFinder
 */

namespace kcfinder;

class storage {
    protected $config = array();
    protected $s3 = null;

    public function __construct($config) {
        $relevant_config = array(
            'storageType',
            's3Key',
            's3Secret',
            's3UseSSL',
            'awsAutoload',
            'tempDir'
        );
        foreach($relevant_config as $key) {
            $this->config[$key] = (!empty($config[$key])) ? $config[$key] : null;
        }
        $this->is_s3();
    }

    protected function is_s3() {
        if (!empty($s3)) {
            return true;
        } else if ($this->config['storageType'] == 'S3') {
            $required_config = array('s3Key', 's3Secret');
            foreach($required_config as $key) if (empty($this->config[$key])) return false;

            if (!empty($this->config['awsAutoload'])) {
                include_once(dirname(__FILE__) . '/../../' . $this->config['awsAutoload']);
            }

            if (!class_exists('\\Aws\\S3\\S3Client')) return false;

            $scheme = (!empty($this->config['s3UseSSL'])) ? 'https' : 'http';
            $s3 = \Aws\S3\S3Client::factory(array(
                'key' => $this->config['s3Key'],
                'secret' => $this->config['s3Secret'],
                'scheme' => $scheme
            ));

            if ($s3) {
                $this->s3 = $s3;
                return true;
            }
        }
        return false;
    }

}