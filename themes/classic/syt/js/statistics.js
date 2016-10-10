/*
 Powered by ly200.com		http://www.ly200.com
 广州联雅网络科技有限公司		020-83226791
 */

var statistics_obj = {
    stat_init: function () {
        $('input[name=Time]').daterangepicker({
            timePicker: false,
            format: 'YYYY/MM/DD',
            dateLimit: true,
            maxDate: true
        });
        $('input[name=Birth]').daterangepicker({
            timePicker: false,
            format: 'YYYY/MM/DD',
            dateLimit: true,
            maxDate: true
        });
        /*frame_obj.chart_par.themes='column';
         frame_obj.chart();
         $('.tab_bar').click(function(){
         switch(frame_obj.chart_par.themes){
         case 'line':
         frame_obj.chart_par.themes='column';
         break;
         case 'column':
         frame_obj.chart_par.themes='line';
         break;
         }
         frame_obj.chart();
         $(this).find('span').html(frame_obj.chart_par.themes=='line'?'柱状图':'曲线图');
         });*/
    }
}