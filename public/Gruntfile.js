module.exports = function(grunt) {
 grunt.initConfig({      	
     cssmin: {       
        website: {
           src: 'template/css/style.css',
           dest: 'template/css/style.min.css'
        }
     }     
 }); 
 grunt.loadNpmTasks('grunt-contrib-cssmin'); 
 grunt.registerTask('default', ['cssmin']);
};