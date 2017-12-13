# Remove any lingering containers from previously failed builds
docker rm my-yarn --force || true
docker rm my-composer --force || true

# Run the Jasmine tests
docker pull sugarcrmdev/school:yarn
docker run -t -d --name my-yarn sugarcrmdev/school:yarn
docker exec my-yarn bash
docker exec my-yarn grunt test-js

# Run the PHPUnit tests
docker pull sugarcrmdev/school:composer
docker run -t -d --name my-composer sugarcrmdev/school:composer
docker exec my-composer bash
docker exec my-composer vendor/bin/phpunit

# Generate the Standard and Windows versions Professor M module loadable package
docker exec my-composer sh -c "cd package && php ./pack.php -v jenkins"
docker exec my-composer sh -c "cd package && php ./pack.php -v jenkins -w 40"

# Create the releases directory in the Jenkins workspace if it does not already exist
# NOTE:  customize the Jenkins workspace to match yours (change "ProfessorM" to the name of your Jenkins project)
mkdir -p /var/jenkins_home/workspace/ProfessorM/package/releases

# Copy the Professor M module loadable packages to the Jenkins workspace
# NOTE:  customize the Jenkins workspace to match yours (change "ProfessorM" to the name of your Jenkins project)
docker cp my-composer:/package/releases/sugarcrm-ProfessorM-jenkins-standard.zip /var/jenkins_home/workspace/ProfessorM/package/releases
docker cp my-composer:/package/releases/sugarcrm-ProfessorM-jenkins-windows.zip /var/jenkins_home/workspace/ProfessorM/package/releases
docker cp my-composer:/package/releases/sugarcrm-ProfessorM-jenkins-windows-manual-install.zip /var/jenkins_home/workspace/ProfessorM/package/releases

# Remove the containers
docker rm my-yarn --force
docker rm my-composer --force
