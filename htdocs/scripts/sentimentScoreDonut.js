let sentimentDataForWordCloud = [];

function prepareSentimentDonut(response){
  console.log(response);
  let pos          = [];
  let neg          = [];
  let neu          = [];

  for(x in response){
    if(response[x].compound_score >= 0.05){
      pos.push({review_id   : response[x].review_id,
                review_text : response[x].review_text});
    }
    else if(response[x].compound_score <= -0.05){
      neg.push({review_id   : response[x].review_id,
                review_text : response[x].review_text});
    }
    else{
      neu.push({review_id   : response[x].review_id,
                review_text : response[x].review_text});
    }
  }
  preparedData.push({Pos :pos,
                     Neg :neg,
                     Neu :neu});

  donutData=[]
  total = preparedData[0].Pos.length + preparedData[0].Neg.length + preparedData[0].Neu.length 
  postiveLabelPercent = 100/total *preparedData[0].Pos.length;
  negativeLabelPercent = 100/total *preparedData[0].Neg.length;
  neutralLabelPercent = 100/total *preparedData[0].Neu.length;

  donutData.push({label  : postiveLabelPercent.toFixed(2) + '% Positive',
                  value: preparedData[0].Pos.length},
                  {label  : negativeLabelPercent.toFixed(2) + '% Negative',
                  value: preparedData[0].Neg.length},
                  {label  : neutralLabelPercent.toFixed(2) + '% Neutral',
                  value: preparedData[0].Neu.length})

  // console.log(preparedData[0].Pos);

  drawSentimentDonut(donutData);
}


function color(key){
  // console.log(key)
  if(key.includes("Positive"))
    return "#79a925";
  else if(key.includes("Negative")){
    return "#f32c22";
  }else{
    return "#99bebe";
  }
}

function drawSentimentDonut(){
  class Donut {
      
    static get defaults() {
      return {
        margin: { top: 15, right: 15, bottom: 30, left: 15 }
      };
    }
    
    constructor(config) {
      this.configure(config);
      this.init();
    }
    
    configure(config) {
      Object.assign(this, Donut.defaults, config);
    }
    
    init() {
      const { margin, data } = this;
      const outerWidth = 400;
      const outerHeight = 400;
      const width = outerWidth - margin.left - margin.right;
      const height = outerHeight - margin.top - margin.bottom;
      const r = Math.min(width, height) / 2;

      var outerRadius =  Math.min(width, height) / 2,
      innerRadius = outerRadius / 1.4;

      var nodeWidth = (d) => d.getBBox().width;

      // const cScale = d3.scaleOrdinal(d3.schemeCategory20b);

      var pie = d3.pie()
      .padAngle(.02)
      .value(d => d.value);

      var arc = d3.arc()
      .padRadius(outerRadius)
      .innerRadius(innerRadius);


      const svg = d3.select('#sentimentScoreContainer')
        .append('svg')
        // .style('border', '1px solid #ddd')
        // .attr('width', outerWidth)
        // .attr('height', outerHeight)
        .attr("viewBox", `0 0 400 400`)
      .append('g')
        .attr('transform', `translate(${margin.left},${margin.top})`);

      let increaseOuterRadiusBy = 8;
      svg.append('g')
        .attr('class', 'arc')
        .attr('transform', `translate(${width/2},${height/2})`)
        .selectAll('path')
        .data(pie(data))
        .enter()
      .append('path')
      .each(function(d) { d.outerRadius = outerRadius ;})
        .attr('d', arc)
        .style('fill', d => color(d.data.label))
        .on("click", function(d) {click(this, d,outerRadius, 150)})
        .on("mouseover", function(d) {mouseover(this,d, outerRadius + increaseOuterRadiusBy, 0)})
        .on("mouseout", function(d) {mouseout(this,d, outerRadius, 150)});
       
      function mouseover(self,d,outerRadius, delay){
        // console.log('MOUSEOVER')
          d3.select(self)
          .transition()
          .delay(delay)
          .attrTween("d", function(d) {
            var i = d3.interpolate(d.outerRadius, outerRadius);
            return function(t) { d.outerRadius = i(t); return arc(d); };
          });
      }

      function mouseout(self, d, outerRadius, delay){
        // console.log('MOUSEOUT')
        if(!d.isClicked || d.isClicked == undefined){
          d3.select(self)
          .classed("word-hovered", true)
          .transition()
          .delay(delay)
          .attrTween("d", function(d) {
            var i = d3.interpolate(d.outerRadius, outerRadius);
            return function(t) { d.outerRadius = i(t); return arc(d); };
          });
        }
      }

      var valuesClicked = []
      function click(self,d,outerRadius, delay){
          d.isClicked = d.isClicked ? false : true;  
          
          // console.log(valuesClicked);
          if(!valuesClicked.includes(d.data.label.split(" ")[1])){
            valuesClicked.push(d.data.label.split(" ")[1]);
          }else{
            let index = valuesClicked.indexOf(d.data.label.split(" ")[1]);
            if (index > -1) {
              valuesClicked.splice(index, 1);
            }
          }
        
          console.log(valuesClicked);

          if(!d.isClicked || d.isClicked == undefined){
            d3.select(self)
            .classed("word-hovered", true)
            .transition()
            .delay(delay)
            .attrTween("d", function(d) {
              var i = d3.interpolate(d.outerRadius, outerRadius);
              return function(t) { d.outerRadius = i(t); return arc(d); };
            });
          }else{
            d3.select(self)
            .classed("word-hovered", true)
            .transition()
            .delay(delay)
            .attrTween("d", function(d) {
              var i = d3.interpolate(d.outerRadius, outerRadius+increaseOuterRadiusBy );
              return function(t) { d.outerRadius = i(t); return arc(d); };
            });
          }
      }

      const legend = svg.append('g')
        .attr('class', 'legend')
        .attr('transform', 'translate(0,0)');

      const lg = legend.selectAll('g')
        .data(data)
        .enter()
      .append('g')
        .attr('transform', (d,i) => `translate(${i * 100},${height + 15})`);

      lg.append('rect')
        .style('fill', d => color(d.label))
        .attr('x', 0)
        .attr('y', 0)
        .attr('width', 20)
        .attr('height', 20);

      lg.append('text')
        .style('font-family', 'Georgia')
        .style('font-size', '15px')
        .attr('x', 23)
        .attr('y', 15)
        .text(d => d.label);

      let offset = 0;
      lg.attr('transform', function(d, i) {
          let x = offset;
          offset += nodeWidth(this) + 10;
          return `translate(${x},${height + 10})`;
      });

      legend.attr('transform', function() {
        return `translate(${(width - nodeWidth(this)) / 2},${0})`
      });
    }
    render(){};
  }

  new Donut({
    element: 'body',
    margin: { top: 10, right: 0, bottom: 30, left: 0},
    data: donutData
  });
}

getSentimentScore();