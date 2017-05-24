<?php
/**
 * Created by PhpStorm.
 * User: mwienk
 * Date: 1/31/17
 * Time: 12:03 PM
 */

namespace Trimm\Jelastic\Service;

use Symfony\Component\Yaml\Yaml;

class ProjectConfig
{
    const JELASTIC_CONFIG_FILE = '.jelastic.yml';
    protected $config;

    public function __construct()
    {
        if (file_exists(self::JELASTIC_CONFIG_FILE)) {
            $this->config = Yaml::parse(file_get_contents(self::JELASTIC_CONFIG_FILE));
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->config[$name];
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->get('prefix');
    }

    /**
     * @return mixed
     */
    public function getBranchPrefix()
    {
        return $this->get('branchPrefix');
    }

    /**
     * @return mixed
     */
    public function getDefaultDomain()
    {
        return $this->get('defaultDomain');
    }

    public function getDomains()
    {
        return $this->get('domains');
    }

    /**
     * @return mixed
     */
    public function getDeploymentNodeType()
    {
        return $this->get('deploymentNodeType');
    }
}