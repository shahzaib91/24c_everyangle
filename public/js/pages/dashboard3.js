//[Dashboard Javascript]

//Project:	Adminto - Responsive Admin Template
//Primary use:   Used only for the main dashboard (index.html)


$(function () {

  'use strict';
	
	 window.Apex = {
		  stroke: {
			width: 3
		  },
		  markers: {
			size: 0
		  },
		  tooltip: {
			  theme: 'dark',
		  }
		};

		var randomizeArray = function (arg) {
		  var array = arg.slice();
		  var currentIndex = array.length,
			temporaryValue, randomIndex;

		  while (0 !== currentIndex) {

			randomIndex = Math.floor(Math.random() * currentIndex);
			currentIndex -= 1;

			temporaryValue = array[currentIndex];
			array[currentIndex] = array[randomIndex];
			array[randomIndex] = temporaryValue;
		  }

		  return array;
		}

		// data for the sparklines that appear below header area
		var sparklineData = [25, 66, 41, 59, 11, 15, 55, 17, 22, 40];
		
		var spark1 = {
		  chart: {
			id: 'spark1',
			group: 'sparks',
			type: 'line',
			height: 100,
			sparkline: {
			  enabled: true
			},
			dropShadow: {
			  enabled: true,
			  top: 5,
			  left: 1,
			  blur: 5,
			  opacity: 0.1,
			}
		  },
		  series: [{
			data: randomizeArray(sparklineData)
		  }],
		  stroke: {
			curve: 'stepline'
		  },
		  markers: {
			size: 0
		  },
		  grid: {
			padding: {
			  top: 10,
			  bottom: 10,
			  left: 0
			}
		  },
		  colors: ['#4e0973'],
		  tooltip: {
			x: {
			  show: false
			},
			y: {
			  title: {
				formatter: function formatter(val) {
				  return '';
				}
			  }
			}
		  }
		}

		var spark2 = {
		  chart: {
			id: 'spark2',
			group: 'sparks',
			type: 'line',
			height: 100,
			sparkline: {
			  enabled: true
			},
			dropShadow: {
			  enabled: true,
			  top: 5,
			  left: 1,
			  blur: 5,
			  opacity: 0.1,
			}
		  },
		  series: [{
			data: randomizeArray(sparklineData)
		  }],
		  stroke: {
			curve: 'stepline'
		  },
		  grid: {
			padding: {
			  top: 10,
			  bottom: 10,
			  left: 0
			}
		  },
		  markers: {
			size: 0
		  },
		  colors: ['#ea9715'],
		  tooltip: {
			x: {
			  show: false
			},
			y: {
			  title: {
				formatter: function formatter(val) {
				  return '';
				}
			  }
			}
		  }
		}

		var spark3 = {
		  chart: {
			id: 'spark3',
			group: 'sparks',
			type: 'line',
			height: 100,
			sparkline: {
			  enabled: true
			},
			dropShadow: {
			  enabled: true,
			  top: 5,
			  left: 1,
			  blur: 5,
			  opacity: 0.1,
			}
		  },
		  series: [{
			data: randomizeArray(sparklineData)
		  }],
		  stroke: {
			curve: 'stepline'
		  },
		  markers: {
			size: 0
		  },
		  grid: {
			padding: {
			  top: 10,
			  bottom: 10,
			  left: 0
			}
		  },
		  colors: ['#fb3d4e'],
		  xaxis: {
			crosshairs: {
			  width: 1
			},
		  },
		  tooltip: {
			x: {
			  show: false
			},
			y: {
			  title: {
				formatter: function formatter(val) {
				  return '';
				}
			  }
			}
		  }
		}

		var spark4 = {
		  chart: {
			id: 'spark4',
			group: 'sparks',
			type: 'line',
			height: 100,
			sparkline: {
			  enabled: true
			},
			dropShadow: {
			  enabled: true,
			  top: 5,
			  left: 1,
			  blur: 5,
			  opacity: 0.1,
			}
		  },
		  series: [{
			data: randomizeArray(sparklineData)
		  }],
		  stroke: {
			curve: 'stepline'
		  },
		  markers: {
			size: 0
		  },
		  grid: {
			padding: {
			  top: 10,
			  bottom: 10,
			  left:0
			}
		  },
		  colors: ['#2a8853'],
		  xaxis: {
			crosshairs: {
			  width: 1
			},
		  },
		  tooltip: {
			x: {
			  show: false
			},
			y: {
			  title: {
				formatter: function formatter(val) {
				  return '';
				}
			  }
			}
		  }
		}

		/*new ApexCharts(document.querySelector("#spark1"), spark1).render();
		new ApexCharts(document.querySelector("#spark2"), spark2).render();
		new ApexCharts(document.querySelector("#spark3"), spark3).render();
		new ApexCharts(document.querySelector("#spark4"), spark4).render();*/
	
	
	
	
	
		
		 var options = {
          series: [44, 55, 67, 83],
          chart: {
          height: 339,
          type: 'radialBar',
        },
		colors: ["#5949d6", "#007eff", "#2a8853", "#fb3d4e"],	 
        stroke: {
			lineCap: "round",
		  },
        plotOptions: {
          radialBar: {
            dataLabels: {
              name: {
				show: false,
              },
              value: {
                fontSize: '24px',
              },
              total: {
                show: true,
                label: 'Total',
                formatter: function (w) {
                  // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                  return 249
                }
              }
            }
          }
        },
        labels: ['Apples', 'Oranges', 'Bananas', 'Berries'],
        };

        /*var chart = new ApexCharts(document.querySelector("#revenue7"), options);
        chart.render();*/
	
	
	
	
		var options = {
          series: [{
          name: 'PRODUCT A',
          data: [44, 55, 41, 67, 22, 43]
        }, {
          name: 'PRODUCT B',
          data: [-44, -55, -41, -67, -22, -43]
        }],
          chart: {
		  foreColor:"#bac0c7",
          type: 'bar',
          height: 355,
          stacked: true,
          toolbar: {
            show: false
          },
          zoom: {
            enabled: true
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            legend: {
              position: 'bottom',
              offsetX: -10,
              offsetY: 0
            }
          }
        }],		
		grid: {
			show: true,
			borderColor: '#f7f7f7',      
		},
		colors:['#5949d6', '#ea9715'],
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '30%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
 
        xaxis: {
          type: 'datetime',
          categories: ['01/01/2011 GMT', '01/02/2011 GMT', '01/03/2011 GMT', '01/04/2011 GMT',
            '01/05/2011 GMT', '01/06/2011 GMT'
          ],
        },
        legend: {
          show: false,
        },
        fill: {
          opacity: 1
        }
        };

        /*var chart = new ApexCharts(document.querySelector("#charts_widget_1_chart"), options);
        chart.render();*/
	
	
	
	    //***************************
       // Stacked Area chart
       //***************************
        /*var stackedbarcolumnChart = echarts.init(document.getElementById('stacked-column'));*/
        var option = {
            
             // Setup grid
                grid: {
                    x: 40,
                    x2: 40,
                    y: 45,
                    y2: 25
                },

                // Add tooltip
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // Axis indicator axis trigger effective
                        type : 'shadow'        // The default is a straight line, optionally: 'line' | 'shadow'
                    }
                },

                // Add legend
                legend: {
                    data: [  'Data1', 'Data2', 'Data3', 'Data4', 'Data5', 'Data7']
                },

                // Add custom colors
                color: ['#5949d6', '#007eff', '#2a8853', '#ea9715', '#fb3d4e'],

                // Enable drag recalculate
                calculable: true,

                // Horizontal axis
                xAxis: [{
                    type: 'category',
                    data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                }],

                // Vertical axis
                yAxis: [{
                    type: 'value',
                }],

                // Add series
                series : [
                    
                    {
                        name:'Data1',
                        type:'bar',
                        stack: 'data1',
                        data:[178, 241, 210, 147, 299, 358, 487]
                    },
                    {
                        name:'Data2',
                        type:'bar',
                        data:[875, 845, 985, 1254, 1425, 1235, 1425],
                        markLine : {
                            itemStyle:{
                                normal:{
                                    lineStyle:{
                                        type: 'dashed'
                                    }
                                }
                            },
                            data : [
                                [{type : 'min'}, {type : 'max'}]
                            ]
                        }
                    },
                    {
                        name:'Data3',
                        type:'bar',
                        barWidth : 12,
                        stack: 'data',
                        data:[654, 758, 754, 854, 1245, 1100, 1140]
                    },
                    {
                        name:'Data4',
                        type:'bar',
                        stack: 'data',
                        data:[104, 134, 125, 158, 245, 236, 278]
                    },
                    {
                        name:'Data5',
                        type:'bar',
                        stack: 'data',
                        data:[54, 123, 147, 85, 165, 158, 123]
                    },
                    {
                        name:'Data6',
                        type:'bar',
                        stack: 'data',
                        data:[21, 84, 79, 86, 135, 158, 210]
                    }
                ]
                // Add series
                
        };
        //stackedbarcolumnChart.setOption(option);
	
	
	var options = {
          series: [{
            name: "Revenue",
            data: [90, 71, 65, 91, 40, 112, 99, 51, 128]
        }],
          chart: {
          height: 189,
          type: 'area',
          zoom: {
            enabled: false
          },			  
		  toolbar: {
			show: false,
		  }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth'
        },
		colors: ['#2a8853'],
        grid: {			
			show: false,
			padding: {
			  top: 0,
			  bottom: 0,
			  right: 0,
			  left: -10
			},
        },
		
		 legend: {
      		show: false,
		 },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
			labels: {
          		show: false,
			},
			axisBorder: {
          		show: false,
			},
			axisTicks: {
          		show: false,
			},
        	},
		
        yaxis: {
          labels: {
          		show: false,
			}
        },
        };

        /*var chart = new ApexCharts(document.querySelector("#revenue6"), options);
        chart.render();*/
	
		$('.countnm').each(function () {
		$(this).prop('Counter',0).animate({
			Counter: $(this).text()
		}, {
			duration: 5000,
			easing: 'swing',
			step: function (now) {
				$(this).text(Math.ceil(now));
			}
		});
	});
	
}); // End of use strict
