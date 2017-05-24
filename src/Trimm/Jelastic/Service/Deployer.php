<?php
/**
 * Created by PhpStorm.
 * User: mwienk
 * Date: 1/26/17
 * Time: 3:22 PM
 */

namespace Trimm\Jelastic\Service;

class Deployer extends ConsoleService
{
    /**
     * @var ProjectConfig
     */
    protected $config;

    /**
     * @var Api
     */
    protected $api;

    public function __construct(ProjectConfig $config, Api $api)
    {
        $this->config = $config;
        $this->api = $api;
    }

    /**
     * @param $branch
     * @param null $prefix
     * @throws \Exception
     */
    public function deploy($branch, $prefix = null)
    {
        if (!$prefix) {
            $prefix = $this->config->getPrefix();
        }
        $newEnvironmentDomain = $this->getCalculatedDomain($branch, $prefix);
        $existingEnvironment = $this->envExists($newEnvironmentDomain);

        if (!$existingEnvironment) {
            $this->logger->info("Environment {$newEnvironmentDomain}.jelastic.trimm.net doesn't exist, I will now clone the default environment.");
            $existingEnvironment = $this->cloneEnvironment($newEnvironmentDomain);
            $this->logger->info("Environment {$newEnvironmentDomain}.jelastic.trimm.net is now available.");
        } else {
            $this->logger->info("Environment exists already, you can start deployment of project.");
        }
        $this->saveNodeIdToFile($existingEnvironment);
        $this->bindDomainNames($branch, $prefix, $newEnvironmentDomain);
        $this->setEnvironmentInfo($newEnvironmentDomain);
    }

    /**
     * Creates a jelastic.json file in the home dir of the user
     * @param $environmentName
     */
    public function setEnvironmentInfo($environmentName)
    {
        $info = $this->api->getEnvInfo($environmentName);
        $encodedInfo = base64_encode(json_encode($info));

        foreach ($info->nodes as $node) {
            $this->api->execCommandById(
                $info->env->envName,
                $node->id,
                [[
                    'command' => 'echo',
                    'params' => '\' ' . json_encode($info) . '\' > ~/jelastic.json'
                ]]
            );

            $this->api->execCommandById(
                $info->env->envName,
                $node->id,
                [[
                    'command' => 'echo',
                    'params' => '\'export JELASTIC_ENV=' . $encodedInfo . '\' > ~/jelastic.env'
                ]]
            );
        }
        $this->logger->info("Added ~/jelastic.json and ~/jelastic.env environment files to the nodes.");
    }

    /**
     * @param $envName
     * @return int
     */
    public function checkEnvironmentStatus($envName)
    {
        $envInfo = $this->api->getEnvInfo($envName);
        if ($envInfo) {
            return $envInfo->env->status;
        } else {
            return 0;
        }
    }

    /**
     * @param $environment
     * @return mixed
     */
    public function getDeploymentNode($environment)
    {
        $deploymentNodeType = $this->config->getDeploymentNodeType();
        foreach ($environment->nodes as $node) {
            if ($node->nodeType == $deploymentNodeType) {
               return $node;
            }
        }
    }

    /**
     * @param $branch
     * @param $prefix
     * @param $suffix
     * @return mixed
     */
    public function getCalculatedDomain($branch, $prefix, $suffix = false)
    {
        $result = preg_replace("/[^A-Za-z0-9]/", '-', $prefix . '-' . $branch);
        if ($suffix) {
           $result .= $suffix;
        }
        return $result;
    }

    /**
     * @param $domain
     * @return mixed
     */
    public function envExists($domain)
    {
        $this->api->signin();
        $envs = $this->api->getEnvs();
        $infos = $envs->infos;
        foreach ($infos as $info) {
            if (strpos($info->env->domain, $domain) === 0) {
                return $info;
            }
        }
        return false;
    }

    /**
     * @param $existingEnvironment
     * @throws \Exception
     */
    public function saveNodeIdToFile($existingEnvironment)
    {
        $deploymentNode = $this->getDeploymentNode($existingEnvironment);
        if ($deploymentNode == null) {
            throw new \Exception("Could not find deployment node (yet).");
        }
        file_put_contents('.jelastic.node', $deploymentNode->id);
        $this->logger->info("Saved node id ({$deploymentNode->id}) to .jelastic.node.");
    }

    /**
     * @param $newEnvironmentDomain
     * @return mixed
     */
    public function cloneEnvironment($newEnvironmentDomain)
    {
        $defaultDomain = $this->config->getDefaultDomain();
        $appId = $this->api->getAppIdForDomain($defaultDomain);
        $this->logger->info("Default environment: {$defaultDomain}");
        $this->logger->info("Default appid:       {$appId}");
        $this->logger->info("New environment:     {$newEnvironmentDomain}");
        $this->api->cloneEnv($appId, $newEnvironmentDomain);
        while ($this->checkEnvironmentStatus($newEnvironmentDomain) == 6) {
            $this->logger->info("{$newEnvironmentDomain}.jelastic.trimm.net still has status 'Creating'.");
            usleep(30000);
        }
        return $this->envExists($newEnvironmentDomain);
    }

    /**
     * @param $branch
     * @param $prefix
     * @param $newEnvironmentDomain
     */
    public function bindDomainNames($branch, $prefix, $newEnvironmentDomain)
    {
        foreach ($this->config->getDomains() as $suffix) {
            $domain = $this->getCalculatedDomain($branch, $prefix, $suffix);
            $this->api->bindExtDomain($newEnvironmentDomain, $domain);
            $this->logger->info("Attached {$domain} to {$newEnvironmentDomain}.");
        }
    }
}