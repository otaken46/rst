/*--------------------
グラフ描画のoptionsで使う色の設定
--------------------*/
//閾値
var red_color = '#FFCAE1'; //opacity:0.3前の色
var yellow_color = '#FFF5BC'; //opacity:0.3前の色
var green_color = '#CAEEFF'; //opacity:0.3前の色
//グラフの色の配列 //RST・心拍数・呼吸数・CSR・臥床時間の順
var data_color_arry = ['#13364D', '#CF252B', '#1B9582', '#2590C9', '#9AA0A8']; //ラインの色
var data_color_arry_fill = ['#13364D', '#CF252B', '#FFFFFF', '#2590C9', '#9AA0A8']; //markerの中の色
var data_color_arry_stroke = ['#13364D', '#CF252B', '#1B9582', '#FFFFFF', '#9AA0A8']; //markerの輪郭の色

/*--------------------
グラフ描画のoptionsで使うデータの設定
--------------------*/
//Y軸の最大値の設定
function maxY() {
  max = Math.max(Math.max.apply(null,arryY_rst[1]), Math.max.apply(null,arryY_heart[1]), Math.max.apply(null,arryY_breath[1])) + 10;
  max = Math.floor(max/5)*5; //5の倍数にする
  //console.log(max);
  return max;
}
//Y軸のメモリ間隔の設定
function tickAmountY() {
  return Math.floor(maxY()/5);
}
//Y軸の設定 (update時にも使用)
var y_axis_setting = [
  {
    seriesName: arryY_rst[0],
    labels: {
      minWidth: 40,
      maxWidth: 40,
      style: {
        fontSize: '14px'
      }
    },
    min: 0,
    decimalsInFloat:0,
    tickAmount: tickAmountY(),
    max: maxY(),
    title: {
      text: "RST・心拍数・呼吸数・臥床時間",
      style: {
        color: '#B3B3B3',
        fontSize: '12px',
        fontWeight: 600,
      }
    },
  },
  { //心拍数のY軸
    seriesName: arryY_rst[0],
    show: false,
  },
  { //呼吸数のY軸
    seriesName: arryY_rst[0],
    show: false,
  },
  { //CSRのY軸は第二軸を作成
    seriesName: arryY_csr[0],
    opposite: true,
    labels: {
      align: 'left',
      minWidth: 40,
      maxWidth: 40,
      offsetX: 2,
      style: {
        fontSize: '14px'
      }
    },
    min: 0,
    decimalsInFloat:1,
    tickAmount: 6,
    max: 3,
    title: {
      text: "CSRグレード",
      style: {
        color: '#B3B3B3',
        fontSize: '12px',
        fontWeight: 600,
      }
    }
  },
  { //臥床時間のY軸
    seriesName: arryY_rst[0],
    show: false,
  }
];

//グラフの高さの設定
function chart_h() {
  if(window.innerWidth > 952) { //凡例が一列に収まっている場合
    var area_h = window.innerHeight - 48 - 44 - 260; //表示領域からグラフ以外の要素の高さを引く
  } else {
    var area_h = window.innerHeight - 48 - 88 - 260; //表示領域からグラフ以外の要素の高さを引く
  }
  var chart_height;
  if(area_h>640){
    chart_height = 640;
  }else if(area_h<320) {
    chart_height = 320;
  }else {
    chart_height = area_h;
  }
  return chart_height;
}

var options = {
  series: [{ //データをセット
    name: arryY_rst[0],
    type: 'line',
    data: arryY_rst[1]
  }, {
    name: arryY_heart[0],
    type: 'line',
    data: arryY_heart[1]
  }, {
    name: arryY_breath[0],
    type: 'line',
    data: arryY_breath[1]
  }, {
    name: arryY_csr[0],
    type: 'line',
    data: arryY_csr[1]
  }, {
    name: arryY_sleep[0],
    type: 'column',
    data: arryY_sleep[1]
  }],
  chart: { //グラフの基本設定
    width: document.getElementById('chart').clientWidth,
    redrawOnParentResize: false,
    redrawOnWindowResize: false,
    height: chart_h(),
    parentHeightOffset: 0,
    type: 'line',
    stacked: false,
    toolbar: {
      show: false
    },
    zoom: {
      enabled: false
    },
    animations: {
      enabled: false,
      easing: 'linear',
      speed: 800,
      animateGradually: {
        enabled: false,
        delay: 100
      },
      dynamicAnimation: {
        enabled: true,
        speed: 400
      }
    },
    events: {
      mounted: function() {
        createMemoLabel(); //メモラベル追加
      },
    },
  },
  colors: data_color_arry,
  stroke: {
    width: [2, 2, 2, 1, 0], //RST・心拍数・呼吸数・CSR・臥床時間の順
    //colors: data_color_arry,
    dashArray: [0, 3, 0, 0, 0],
    curve: 'smooth'
  },
  markers: {
    size: [3, 3, 2.5, 2.5, 0], //RST・心拍数・呼吸数・CSR・臥床時間の順
    colors: data_color_arry_fill,
    strokeColors: data_color_arry_stroke,
    strokeWidth: [0, 0, 2, 1, 0],
    hover: {
      size: 7
    },
  },
  fill: {
    //colors: data_color_arry,
    opacity: 1,
  },
  plotOptions: {
    bar: {
      columnWidth: '50%'
    }
  },
  xaxis: {
    //categories: arryX_moto,
    tickAmount: dayRange,
    categories: arryX_moto,
    labels: {
      /*formatter: function (value, timestamp) {
        var d = new Date(value).getDate();
        return d;
      },*/
      rotate: -90,
      rotateAlways: true,
      style: {
        fontSize: '5px',
      }
    },
    tooltip: {
      enabled: false
    }
  },
  yaxis: y_axis_setting,
  tooltip: {
    shared: true,
    //fillSeriesColor: data_color_arry,
    style: {
        fontSize: '14px',
        fontFamily: 'Helvetica, Arial, sans-serif'
    },
    x: {
      formatter: function(value) {
        //var fullDateObj = new Date(arryX_moto[value - 1]);
        var fullDate = arryX_moto[value - 1]; //yyyy/mm/dd
        fullDate = fullDate.replace(/-/g, "/");
        if(arryY_memo[1][value - 1]){
          return ("<span style='opacity:0.7; font-size:16px;'>" + fullDate + "</span><br>" + arryY_memo[1][value - 1]);
        } else {
          return ("<span style='opacity:0.7; font-size:16px;'>" + fullDate + "</span>");
        }
      }
    },
    y: {
      formatter: function (y) {
        return y;
      },
    },
  },
  grid: {
    show: true,
    borderColor: '#E7E7E7',
    xaxis: {
      lines: {
        show: true
      }
    },
    yaxis: {
      lines: {
        show: true
      }
    },
    row: {
      opacity: 0
    },
    column: {
      opacity: 0
    },
    padding: {
      top: 0,
      right: 10,
      //bottom: 20, //メモラベル(xAxis annotation label)のためのスペース
      bottom: 0,
      left: 10
    },
  },
  annotations: { //閾値の背景
    position: 'back',
    yaxis: [{
      y: red_min,
      y2: red_max,
      strokeDashArray: 0,
      borderColor: null,
      fillColor: red_color,
    }, {
      y: yellow_min,
      y2: yellow_max,
      strokeDashArray: 0,
      borderColor: null,
      fillColor: yellow_color,
    }, {
      y: green_min,
      y2: green_max,
      strokeDashArray: 0,
      borderColor: null,
      fillColor: green_color,
    }],
  },
  legend: {
    show: false,
  },
};
var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();

//X軸のメモリとメモのラベルを追加
function createMemoLabel() {
  var memoStyle = {position: 'bottom',borderColor: null ,orientation: 'horizontal',text: '◆' ,offsetY: 40, style:{fontSize: '12px', background: 'rgba(255,255,255,0)'}};
  for(var i=0; i<arryY_memo[1].length; i++){
    var date = arryX_moto[i].split("-");
    date = date[date.length - 1].replace(/^0+/, ""); //一桁の日付は0を削除
    var xLavelStyle = {position: 'bottom',borderColor: null ,orientation: 'horizontal',text: date ,offsetY: 20, style:{fontSize: '14px'}};
    if(arryY_memo[1][i]){
      chart.addXaxisAnnotation({ //メモのラベル
        id: 'xLabel',
        x: arryX_moto[i],
        label: memoStyle
      },false);
    }
    if(arryX_moto.length < 31){
      chart.addXaxisAnnotation({ //X軸のメモリ(日付のみ)
        id: 'xTick',
        x: arryX_moto[i],
        label: xLavelStyle
      },false);
    } else if(arryX_moto.length < 61){
      if(i%2 === 0){ //1個飛ばし
        chart.addXaxisAnnotation({ //X軸のメモリ(日付のみ)
          id: 'xTick',
          x: arryX_moto[i],
          label: xLavelStyle
        },false);
      }
    } else if(arryX_moto.length > 59){
      if(i%3 === 0){ //2個飛ばし
        chart.addXaxisAnnotation({ //X軸のメモリ(日付のみ)
          id: 'xTick',
          x: arryX_moto[i],
          label: xLavelStyle
        },false);
      }
    }
  }
}
