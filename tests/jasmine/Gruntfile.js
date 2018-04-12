module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        jasmine: {
            components: {
                src: [
                    '../../package/*js'
                ],
                options: {
                    specs: 'specs/*Spec.js',
                    keepRunner: false
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-jasmine');

    grunt.registerTask('test-js', ['jasmine']);

};
