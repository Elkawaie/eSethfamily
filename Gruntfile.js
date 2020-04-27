module.exports = function(grunt) {
    grunt.initConfig({
        sass: {
            options: {
                includePaths: ['node_modules/bootstrap-sass/assets/stylesheets']
            },
            dist: {
                options: {
                    outputStyle: 'compressed'
                },
                files: [{
                    'public/css/bootstrap.min.css': 'assets/vendor/bootstrap/css/bootstrap.min.css', /* Bootstrap CSS */
                    'public/css/font-awesome.min.css': 'assets/vendor/font-awesome/css/font-awesome.min.css',
                    'public/css/chartist.min.css': [                                                   /* Chartist css */
                        'assets/vendor/css/chartist.min.css',
                        'assets/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css'
                    ],
                    'public/css/dataTable.bundle.css': [
                        'assets/vendor/jquery-datatable/dataTables.bootstrap4.min.css',
                        'assets/vendor/jquery-datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css',
                        'assets/vendor/jquery-datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css',
                        'assets/vendor/sweetalert/sweetalert.css',
                    ],
                    'public/css/toastr.min.css': 'assets/vendor/toastr/toastr.min.css',         /* Toastr css */
                    'public/css/main.css': 'assets/scss/main.scss', 	                        /* 'All main SCSS' */
                    'public/css/color_skins.css': 'assets/scss/color_skins.scss', 				/* 'All Color Option' */
                    'public/css/chatapp.css': 'assets/scss/pages/chatapp.scss', 				/* 'Chat App SCSS to CSS' */
                    'public/css/inbox.css': 'assets/scss/pages/inbox.scss', 				    /* 'Inbox App SCSS to CSS' */

                }]
            }
        },
        uglify: {
            my_target: {
                files: {
                    'public/bundles/libscripts.bundle.js': ['assets/vendor/jquery/jquery-3.3.1.min.js','assets/vendor/bootstrap/js/popper.min.js','assets/vendor/bootstrap/js/bootstrap.js'], /* main js*/
                    'public/bundles/vendorscripts.bundle.js': ['assets/vendor/metisMenu/metisMenu.js','assets/vendor/jquery-slimscroll/jquery.slimscroll.min.js','assets/vendor/bootstrap-progressbar/js/bootstrap-progressbar.min.js','assets/vendor/jquery-sparkline/js/jquery.sparkline.min.js'], /* coman js*/

                    'public/bundles/mainscripts.bundle.js':['assets/js/common.js'], /*coman js*/

                    'public/bundles/morrisscripts.bundle.js': ['assets/vendor/raphael/raphael.min.js','assets/vendor/morrisjs/morris.js'], /* Morris Plugin Js */
                    'public/bundles/knob.bundle.js': ['assets/vendor/jquery-knob/jquery.knob.min.js'], /* knob js*/
                    'public/bundles/chartist.bundle.js':['assets/vendor/chartist/js/chartist.min.js','assets/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.min.js','assets/vendor/chartist-plugin-axistitle/chartist-plugin-axistitle.min.js','assets/vendor/chartist-plugin-legend-latest/chartist-plugin-legend.js','assets/vendor/chartist/Chart.bundle.js'], /*chartist js*/

                    'public/bundles/fullcalendarscripts.bundle.js': ['assets/vendor/fullcalendar/moment.min.js'],
                    'public/bundles/jvectormap.bundle.js': ['assets/vendor/jvectormap/jquery-jvectormap-2.0.3.min.js','assets/vendor/jvectormap/jquery-jvectormap-world-mill-en.js'],   /* calender page js */
                    'public/bundles/easypiechart.bundle.js': ['assets/vendor/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js','assets/vendor/jquery.easy-pie-chart/easy-pie-chart.init.js'],

                    'public/bundles/datatablescripts.bundle.js': [
                        'assets/vendor/jquery-datatable/jquery.dataTables.min.js',
                        'assets/vendor/jquery-datatable/dataTables.bootstrap4.min.js'
                    ], /* Jquery DataTable Plugin Js  */
                    'public/bundles/dataTableButton.bundle.js': [
                        'assets/vendor/jquery-datatable/buttons/dataTables.buttons.min.js',
                        'assets/vendor/jquery-datatable/buttons/buttons.bootstrap4.min.js',
                        'assets/vendor/jquery-datatable/buttons/buttons.colVis.min.js',
                        'assets/vendor/jquery-datatable/buttons/buttons.html5.min.js',
                        'assets/vendor/jquery-datatable/buttons/buttons.print.min.js'
                    ],
                    // 'public/bundles/sweetalert.min.js': 'assets/vendor/sweetalert/sweetalert.min.js',
                    'public/bundles/flotscripts.bundle.js': ['assets/vendor/flot-charts/jquery.flot.js','assets/vendor/flot-charts/jquery.flot.resize.js','assets/vendor/flot-charts/jquery.flot.pie.js','assets/vendor/flot-charts/jquery.flot.categories.js','assets/vendor/flot-charts/jquery.flot.time.js'], /* Flot Chart js*/
                    'public/js/index.js': 'assets/js/index.js',
                    'public/bundles/toastr.js': 'assets/vendor/toastr/toastr.js',
                    'public/bundles/jquery.flot.selection.js': 'assets/vendor/flot-charts/jquery.flot.selection.js'

                }
            }
        }
    });
    grunt.loadNpmTasks("grunt-sass");
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask("buildcss", ["sass"]);
    grunt.registerTask("buildjs", ["uglify"]);
};