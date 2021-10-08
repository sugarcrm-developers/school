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
                    keepRunner: false,
                    // workaround https://github.com/gruntjs/grunt-contrib-jasmine/issues/339
                    noSandbox: true,
                    version: '3.8.0',
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-jasmine');

    grunt.registerTask('test-js', ['jasmine']);

};
