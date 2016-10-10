// JavaScript Document

//交易状况圆饼图
$(function () {
    /*
    * 交易金额图表部分
    * */
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('container'));
    option = {
        title: {
            text: '交易金额',
            x:'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b}: {c} ({d}%)"
        },
        color: [
            "#3cb035", "#469DF7", "#ccc"
        ],
        legend: {
            orient: 'horizontal',
            right: 'center',
            bottom:0,
            data:['支付宝','微信','其他']
        },
        series : [
            {
                name: '访问来源',
                type: 'pie',
                radius: ['40%', '60%'],
                data:[
                    {value:235, name:'支付宝'},
                    {value:274, name:'微信'},
                    {value:310, name:'其他'}
                ],
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);

    /*
     * 交易笔数图表部分
     * */
    // 基于准备好的dom，初始化echarts实例
    var myChart1 = echarts.init(document.getElementById('container01'));
    option1 = {
        title: {
            text: '交易金额',
            x:'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b}: {c} ({d}%)"
        },
        color: [
            "#3cb035", "#469DF7", "#ccc"
        ],
        legend: {
            orient: 'horizontal',
            right: 'center',
            bottom:0,
            data:['支付宝','微信','其他']
        },
        series : [
            {
                name: '访问来源',
                type: 'pie',
                radius: ['40%', '60%'],
                data:[
                    {value:235, name:'支付宝'},
                    {value:274, name:'微信'},
                    {value:310, name:'其他'}
                ],
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart1.setOption(option1);

    /*
    * 交易趋势-交易金额
    * */
    var myChart2 = echarts.init(document.getElementById('containerMoney'));
    option2 = {

        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b} : {c}'
        },
        legend: {
            right: 'center',
            data: ['支付宝', '微信']
        },
        xAxis: {
            type: 'category',
            name: '时间',
            splitLine: {
                show: false
            },
            data: ['2016-4-18', '2016-4-19', '2016-4-20', '2016-4-21', '2016-4-22', '2016-4-23', '2016-4-24', '2016-4-25']
        },
        color: [
            "#469DF7", "#3cb035"
        ],
        grid: {
            left: '3%',
            right: '5%',
            bottom: '3%',
            containLabel: true
        },
        yAxis: {
            type: 'log',
            name: '实收金额(元)'
        },
        series: [{
            name: '支付宝',
            type: 'line',
            data: [1, 30, 9, 20, 281, 247, 741, 2223, 6669]
        }, {
            name: '微信',
            type: 'line',
            data: [1, 2, 4, 8, 16, 32, 64, 128, 256]
        }, ]
    };
    myChart2.setOption(option2);

    /*交易趋势-交易笔数*/
    var myChart3 = echarts.init(document.getElementById('containertimes'));
    option3 = {

        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b} : {c}'
        },
        legend: {
            right: 'center',
            data: ['支付宝', '微信']
        },
        xAxis: {
            type: 'category',
            name: '时间',
            splitLine: {
                show: false
            },
            data: ['2016-4-18', '2016-4-19', '2016-4-20', '2016-4-21', '2016-4-22', '2016-4-23', '2016-4-24', '2016-4-25']
        },
        color: [
            "#469DF7", "#3cb035"
        ],
        grid: {
            left: '3%',
            right: '5%',
            bottom: '3%',
            containLabel: true
        },
        yAxis: {
            type: 'log',
            name: '交易笔数(笔)'
        },
        series: [{
            name: '支付宝',
            type: 'line',
            data: [1, 3, 9, 27, 81, 247, 741, 2223]
        }, {
            name: '微信',
            type: 'line',
            data: [1, 2, 4, 8, 16, 32, 64, 128]
        }, ]
    };
    myChart3.setOption(option3);

});