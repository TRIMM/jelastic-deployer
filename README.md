Jelastic Deployer
========================

You can use the docker jelastic deployer tool to deploy your project to Jelastic.

Create a `.jelastic.yml` file in the root of your repository, like the following:

```yaml
prefix: 'projectx'
defaultDomain: 'projectx-default-dev.jelastic.yourdomain.net'
branchPrefix: 'feature'
deploymentNodeType: 'apache2'
domains:
    - '-region-usa.mytld.com'
    - '-region-uk.mytld.com'
```

Then use the ./bin/console command to execute your commands.

```
console jelastic:signin $USER $PASSWORD $PLATFORM_URL
console jelastic:clone-env $APPID $DOMAIN
console jelastic:get-envs
console jelastic:deploy-env $BRANCH [$PREFIX]
```