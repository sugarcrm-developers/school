module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        jasmine: {
            components: {
                src: [
                    'package/*js'
                ],
                options: {
                    specs: 'tests/jasmine/*Spec.js',
                    keepRunner: true
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-jasmine');

    grunt.registerTask('test-js', ['jasmine']);
};