
function prepareSentimentDonut(total){

  let donutData=[]
  // total = sentimentDataForWordCloud[0].Positive.length + sentimentDataForWordCloud[0].Negative.length + sentimentDataForWordCloud[0].Neutral.length 
  // console.log(total)
  postiveLabelPercent  = 100/total *sentimentDataForWordCloud[0].Positive.length;
  negativeLabelPercent = 100/total *sentimentDataForWordCloud[0].Negative.length;
  neutralLabelPercent  = 100/total *sentimentDataForWordCloud[0].Neutral.length;

  donutData.push({label  : postiveLabelPercent.toFixed(1) + '% Positive',
                  value: sentimentDataForWordCloud[0].Positive.length},
                  {label  : negativeLabelPercent.toFixed(1) + '% Negative',
                  value: sentimentDataForWordCloud[0].Negative.length},
                  {label  : neutralLabelPercent.toFixed(1) + '% Neutral',
                  value: sentimentDataForWordCloud[0].Neutral.length})

  // console.log(donutData);
  drawSentimentDonut(donutData);
}

function color(key){
  // console.log(key)
  if(key.includes("Positive"))
    return "#79a925";
  else if(key.includes("Negative")){
    return "#FF4136";
  }else{
    return "#AAAAAA";
  }
}

function drawSentimentDonut(donutData){
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
      // var pie = d3.layout.pie()
      .padAngle(.02)
      .value(d => d.value);

      var arc = d3.arc()
      // var arc = d3.svg.arc()
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
        .attr('id', d=>d.data.label.split(" ")[1]) //
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
        })
        .attr("stroke","black")
        .attr("stroke-width",1);
        
        showToolTip(d)
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
          })
          .attr("stroke","none")
          .attr("stroke-width",0);
        }

        hideToolTip()
      }

      var valuesClicked = []
      function click(self,d,outerRadius, delay){
        d.isClicked = d.isClicked ? false : true;  

        if(self.id != 'Positive'){
          // console.log("removing positive")
          d3.select('#Positive')['_groups'][0][0]['__data__']['isClicked'] = false;
          d3.select('#Positive')
          .classed("word-hovered", true)
          .transition()
          .delay(delay)
          .attrTween("d", function(d) {
            var i = d3.interpolate(d.outerRadius, outerRadius);
            return function(t) { d.outerRadius = i(t); return arc(d); };
          })
          .attr("stroke","none")
          .attr("stroke-width",0);
        }

        if(self.id != 'Negative'){
          // console.log("removing negative")
          d3.select('#Negative')['_groups'][0][0]['__data__']['isClicked'] = false;
          d3.select('#Negative')
          .classed("word-hovered", true)
          .transition()
          .delay(delay)
          .attrTween("d", function(d) {
            var i = d3.interpolate(d.outerRadius, outerRadius);
            return function(t) { d.outerRadius = i(t); return arc(d); };
          })
          .attr("stroke","none")
          .attr("stroke-width",0);
        }

        if(self.id != 'Neutral'){
          // console.log("removing neutral")
          d3.select('#Neutral')['_groups'][0][0]['__data__']['isClicked'] = false;
          d3.select('#Neutral')
          .classed("word-hovered", true)
          .transition()
          .delay(delay)
          .attrTween("d", function(d) {
            var i = d3.interpolate(d.outerRadius, outerRadius);
            return function(t) { d.outerRadius = i(t); return arc(d); };
          })
          .attr("stroke","none")
          .attr("stroke-width",0);
        }
  
        document.getElementById("wordCloudNotEnoughWordsWarning").style.display = "none";
        document.getElementById("wordCloudContainer").style.display             = "block";

        let dataToBeSentToServer = [{data:[]}];
        // Show all reviews wordCloud if nothing selected
        if(valuesClicked[0] == self.id){
          valuesClicked = []

          let storeIDByUser = document.getElementById('getStoreID').value;
          let shopID = (storeIDByUser == null) ? '1' : storeIDByUser;

          let url = hostname + "/adj_noun_pairs/" + shopID;
          retrieveWordCloudNounAdjPairs(url,"GET","");
          hideToolTip() 

        }
        // Show just the wordCloud for selected sentiment
        else{
          document.getElementById('wordCloudContainer').scrollIntoView({block: "end",behavior:'smooth'});

          valuesClicked = []
          valuesClicked.push(self.id)
          if(sentimentDataForWordCloud[0][self.id].length > 10){
            dataToBeSentToServer[0].data.push(...sentimentDataForWordCloud[0][self.id])
            let url = hostname + "/adj_noun_pairs/";
            retrieveWordCloudNounAdjPairs(url,"POST",JSON.stringify(dataToBeSentToServer[0]));
          }
          else{
            // console.log(sentimentDataForWordCloud[0][self.id])
            selectedReview = sentimentDataForWordCloud[0][self.id] //the main variable is in the first few lines of dashboard.php
            console.log("throw warning")
            document.getElementById("wordCloudContainer").style.display          = "none";
            document.getElementById("wordCloudNotEnoughWordsWarning").innerHTML  = 
            `<!-- Triangle with exclamation icon -->
            <div style="font-size:50px; color: #fdcc0d; position: absolute; top: 40%;left: 46%;" class="material-icons">
                  warning_amber  
            </div>
            <!-- Warning message that goes along with the above icon -->
            <div class='ml-5 mr-5 text-center' style='position: absolute;top: 52%'>
              Not enough reviews to display wordcloud. 
              Click <a href="javascript:void(0)" onclick="displayReviewsBelowWordCloud_BelowTenReviews(selectedReview)">here</a>
               to view the ${sentimentDataForWordCloud[0][self.id].length} ${(self.id).toLowerCase()} reviews instead
            </div>`
            document.getElementById("wordCloudNotEnoughWordsWarning").style.display = "block";
          }
        }
        
    
        if(!d.isClicked || d.isClicked == undefined){
          d3.select(self)
          .classed("word-hovered", true)
          .transition()
          .delay(delay)
          .attrTween("d", function(d) {
            var i = d3.interpolate(d.outerRadius, outerRadius);
            return function(t) { d.outerRadius = i(t); return arc(d); };
          })
          .attr("stroke","none")
          .attr("stroke-width",0);
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
      
      // Show Tooltip
      function showToolTip(d, i) {
        let addSpaceFromLeft = 0
        if(d.data.value < 10){
          addSpaceFromLeft = 10
        }else if(d.data.value < 100){
          addSpaceFromLeft = 5
        }else if(d.data.value < 1000){
          addSpaceFromLeft = 2
        }

        var tooltip = svg.append("g")
          .attr("class", "ToolTip")
              
          tooltip.append("rect")
          .attr("width", 130)
          .attr("height", 35)
          .attr("x",140)
          .attr("y",170)
          .attr("fill", "black")
          .style("opacity", 0.75);

          tooltip.append("text")
          .attr("x",145+addSpaceFromLeft)
          .attr("y",194)
          .attr("fill","white")
          .text(d.data.value + " Review(s)");
      }
      
      // Hide the Tooltip
      function hideToolTip(d, i) {
        svg.select(".ToolTip").remove();
      }

      const legend = svg.append('g')
        .attr('class', 'legend')
        .attr('transform', 'translate(0,0)');

      // legend.selectAll('g').data(console.log(data))
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

  // console.log(donutData)

  new Donut({
    element: 'body',
    margin: { top: 10, right: 0, bottom: 30, left: 0},
    data: donutData
  });
}