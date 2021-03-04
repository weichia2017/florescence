let preparedData  = [];
var width         = $(window).width(), height = $(window).height();

async function prepareWordCloud(){
    let storeIDByUser = document.getElementById('getStoreID').value;
    let shopID = (storeIDByUser == null) ? '1' : storeIDByUser;

    var adjNounPairs      = await makeRequest("http://35.175.55.18:5000/adj_noun_pairs/" + shopID, "GET", "");
    let response          = JSON.parse(adjNounPairs).data;
    // let response          = JSON.parse(jsonResponse).data;

    let fontsizeIdentifierCount = 0;
    for(x in response){
      fontsizeIdentifierCount += response[x].COUNT
    }
    fontsizeIdentifierCount /=10;
    console.log(response)
    let accumulatedAdj    = [];
    let totalCountForNoun = 0;
    for(x in response){
        let currentNoun   = response[x].NOUN;
        let count         = response[x].COUNT;
        let adj           = response[x].ADJ;
        let reviewid      = 'N.A';
        if(response[x].REVIEWID != undefined){
            reviewid      = response[x].REVIEWID;
        }
       
        totalCountForNoun += count;
        //adj parameter 0,1,2: the adj, the count of adj, the reviewID of adj
        accumulatedAdj.push([adj,count,reviewid]);

        if( (response[parseInt(x)+1] !== undefined && currentNoun != response[parseInt(x)+1].NOUN) || 
            response[parseInt(x)+1] === undefined){
            
            console.log(fontsizeIdentifierCount);
            let multiplier = 0
            if(fontsizeIdentifierCount >30){
              multiplier= 30;
            }else if(fontsizeIdentifierCount >25){
              multiplier= 35;
            }else if(fontsizeIdentifierCount >20){
              multiplier= 40;
            }else if(fontsizeIdentifierCount >15){
              multiplier= 45;
            }else if(fontsizeIdentifierCount >10){
              multiplier= 50;
            }else if(fontsizeIdentifierCount >5){
              multiplier = 65;
            }else if(fontsizeIdentifierCount >1){
              multiplier = 95;
            }

            if(totalCountForNoun==1){
              totalCountForNoun = 2;
            }

            // adj parameter 3: adj sizes
            let wordCloudSize = Math.log10(totalCountForNoun)*multiplier;
            let adjOneSize   = wordCloudSize/6 *2.5;
            let adjTwoSize   = wordCloudSize/6 *2;
            let adjThreeSize = wordCloudSize/6 *1.5;

            fontFamily = "'Anton', sans-serif";
            spaceBetweenNounAdj = 3;
            let nounLength = getTextLength(currentNoun,wordCloudSize,fontFamily) /2;

            // adj parameter 4: How much the adj shld move in the x axis
            let adjOneX   =  (nounLength + spaceBetweenNounAdj);
            let adjTwoX   =  (nounLength + spaceBetweenNounAdj);
            let adjThreeX =  (nounLength + spaceBetweenNounAdj);

            // adj parameter 5: How much the adj shld move in the y axis

            if(accumulatedAdj.length == 1){
              var adjOneY   = wordCloudSize/6 * 1.5;
            }

            if(accumulatedAdj.length == 2){
              var adjOneY   = wordCloudSize/6 * 2.2;
              var adjTwoY   = wordCloudSize/6 * 0.3;
            }
            
            if(accumulatedAdj.length == 3){
              var adjOneY   = wordCloudSize/6 * 3;
              var adjTwoY   = wordCloudSize/6 * 1.2;
              var adjThreeY = 3;
            }

            accumulatedAdj[0].push(adjOneSize,adjOneX,adjOneY);
            if (accumulatedAdj[1] != undefined)
              accumulatedAdj[1].push(adjTwoSize,adjTwoX,adjTwoY);
            if (accumulatedAdj[2] != undefined)
              accumulatedAdj[2].push(adjThreeSize,adjThreeX,adjThreeY);

            let newTempNoun = getNewNounDupe(accumulatedAdj,fontFamily,spaceBetweenNounAdj,nounLength,wordCloudSize,currentNoun);

            preparedData.push({ noun  : newTempNoun,
                                adj   : accumulatedAdj,
                                count : totalCountForNoun,
                                size  : wordCloudSize,
                                font  : fontFamily
                            });
            totalCountForNoun = 0;
            accumulatedAdj    = [];
        }
    }
    console.log(preparedData)

    let w = document.getElementById('wordCloudContainer').offsetWidth;
    let h = 450;

    drawWordcLOUD(w,h);
}

function getTextLength(text,size,fontFamily){

    var BrowserText = (function () {
        var canvas = document.createElement('canvas'),
            context = canvas.getContext('2d');
    
        /**
         * Measures the rendered width of arbitrary text given the font size and font face
         * @param {string} text The text to measure
         * @param {number} fontSize The font size in pixels
         * @param {string} fontFace The font face ("Arial", "Helvetica", etc.)
         * @returns {number} The width of the text
         **/
        function getWidth(text, fontSize, fontFace) {
            context.font = fontSize + 'px ' + fontFace;
            return context.measureText(text).width;
        }
    
        return {
            getWidth: getWidth
        };
    })();

    return BrowserText.getWidth(text, size, fontFamily);
}

function getNewNounDupe(adjArray,fontFamily,spaceBetweenNounAdj,nounLength,nounSize,nounText){
    let longestAdj = 0;
    for (x in adjArray){
        length = getTextLength(adjArray[x][0],adjArray[x][3],fontFamily);

        if(length > longestAdj){
            longestAdj = length;
        }
    }

    totalLength = longestAdj + spaceBetweenNounAdj + (nounLength*2);

    let newNounText = "."+nounText;

    do{
        newNounText = "l" + newNounText;
    }
    while (getTextLength(newNounText,nounSize,fontFamily) <= totalLength)

    return "ll" + newNounText;
}

/* Each time the window gets resized, 
*   1. get the new width and height of the container
*   2. remove inner HTML of word cloud
*   3. draw a new wordcloud
/=*/ 
function resize(){
    // console.log(document.getElementById('wordCloudContainer').offsetWidth)
    // console.log(document.getElementById('wordCloudContainer').offsetHeight)
    w = document.getElementById('wordCloudContainer').offsetWidth;
    h = document.getElementById('wordCloudContainer').offsetHeight;

    if($(window).width() != width || $(window).height() != height){
        removeWordCloud();
        drawWordcLOUD(w,h);
    }
}

function removeWordCloud(){
    document.getElementById("wordCloudContainer").innerHTML = "";
}

function randomColor () {
    var chars = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++)
        color += chars[Math.floor(Math.random() * 16)];
    return color;
}

function drawWordcLOUD(w,h){     
    // set the dimensions and margins of the graph
    var margin = {top: 5, right: 5, bottom: 5, left: 5},
    width = w - margin.left - margin.right,
    height = h - margin.top - margin.bottom;

    // append the svg object to the body of the page
    var svg = d3.select("#wordCloudContainer").append("svg")
        // .attr("width", width + margin.left + margin.right)
        // .attr("height", height + margin.top + margin.bottom)
        .attr("viewBox", `0 0 ${w} ${h}`)
    .append("g")
        .attr("transform",
            "translate(" + margin.left + "," + margin.top + ")");

    // Constructs a new cloud layout instance. It run an algorithm to find the position of words that suits your requirements
    // Wordcloud features that are different from one word to the other must be here
    var wordCloudSVG = d3.layout.cloud() 
    .size([width, height])
    .words(preparedData.map(function(d) { return {text: d.noun, size:d.size, adjArray: d.adj, fontFam:d.font}; }))
    // .spiral("rectangular")
    .padding(11)       //space between words
    .rotate(0)         // To rotate -> function() { return ~~(Math.random() * 2) * 90; }
    .fontSize(function(d) { return d.size})  // Originial is just d.size ...; Log Math.log10(d.size)*60; Initiall used Math.abs(d.size - average)/average * 60
    .on("word", ({size, x, y, rotate, text, adjArray,fontFam}) => {
        // console.log(x)
        let nounSize = size;
        let nounX    = x;
        let nounY    = y;

        //NOUN
        svg.append("text")
          .attr("font-size", nounSize)
          .attr("text-anchor", "middle")
          .style("font-family", fontFam)
          .attr("transform", `translate(${nounX},${nounY}) rotate(${rotate})`)
          .text(text.split(".")[1])
          .classed("click-only-text", true)
          .style("fill", color => randomColor());

        //ADJ1
        let adjTextOne   = adjArray[0][0];
        let adjOneSize   = adjArray[0][3];
        let adjOneX   = x - adjArray[0][4];
        let adjOneY   = y - adjArray[0][5];

        svg.append("text")
          .attr("font-size", adjOneSize)
          .attr("text-anchor", "end")
          .attr("id",  adjArray[0][2])
          .style("font-family", fontFam)
          .attr("transform", `translate(${adjOneX},${adjOneY}) rotate(${rotate})`)
          .text(adjTextOne)
          .classed("click-only-text", true)
          .style("fill", color => randomColor())
          .on("mouseover", handleMouseOverAdjOne)
          .on("mouseout", handleMouseOutAdjOne)
          .on("click", handleClick);
        
          function handleMouseOverAdjOne(d, i) {
            d3.select(this)
              .classed("word-hovered", true)
              .transition(`mouseover-${adjTextOne}`).duration(300).ease(d3.easeLinear)
                .attr("font-size", adjOneSize + 3);
          }
          
          function handleMouseOutAdjOne(d, i) {
            d3.select(this)
              .classed("word-hovered", false)
              .interrupt(`mouseover-${adjTextOne}`)
                .attr("font-size", adjOneSize);
          }
          
          function handleClick(d, i) {
            var e = d3.select(this);
            console.log(e._groups[0][0].id)
            e.classed("word-selected", !e.classed("word-selected"));
          }

        //ADJ2
        if (adjArray[1] != undefined){
          let adjTextTwo   = adjArray[1][0];
          let adjTwoSize   = adjArray[1][3];
          let adjTwoX   = x - adjArray[1][4];
          let adjTwoY   = y - adjArray[1][5];

          svg.append("text")
          .attr("font-size", adjTwoSize)
          .attr("text-anchor", "end")
          .attr("id",  adjArray[1][2])
          .style("font-family", fontFam)
          .attr("transform", `translate(${adjTwoX},${adjTwoY}) rotate(${rotate})`)
          .text(adjTextTwo)
          .classed("click-only-text", true)
          .style("fill", color => randomColor())
          .on("mouseover", handleMouseOverAdjTwo)
          .on("mouseout", handleMouseOutAdjTwo)
          .on("click", handleClick);
                
          function handleMouseOverAdjTwo(d, i) {
            d3.select(this)
              .classed("word-hovered", true)
              .transition(`mouseover-${adjTextTwo}`).duration(300).ease(d3.easeLinear)
                .attr("font-size", adjTwoSize + 3);
          }
          
          function handleMouseOutAdjTwo(d, i) {
            d3.select(this)
              .classed("word-hovered", false)
              .interrupt(`mouseover-${adjTextTwo}`)
                .attr("font-size", adjTwoSize);
          }
          
          function handleClick(d, i) {
            var e = d3.select(this);
            console.log(e._groups[0][0].id)
            e.classed("word-selected", !e.classed("word-selected"));
          }
        }

        //ADJ3
        if (adjArray[2] != undefined){
          let adjTextThree = adjArray[2][0];
          let adjThreeSize = adjArray[2][3];
          let adjThreeX = x - adjArray[2][4];
          let adjThreeY = y + adjArray[2][5];

          svg.append("text")
          .attr("font-size", adjThreeSize)
          .attr("text-anchor", "end")
          .attr("id",  adjArray[2][2])
          .style("font-family", fontFam)
          .attr("transform", `translate(${adjThreeX},${adjThreeY}) rotate(${rotate})`)
          .text(adjTextThree)
          .classed("click-only-text", true)
          .style("fill", color => randomColor())
          .on("mouseover", handleMouseOverAdjThree)
          .on("mouseout", handleMouseOutAdjThree)
          .on("click", handleClick);
                
          function handleMouseOverAdjThree(d, i) {
            d3.select(this)
              .classed("word-hovered", true)
              .transition(`mouseover-${adjTextThree}`).duration(300).ease(d3.easeLinear)
                .attr("font-size", adjThreeSize + 3);
          }
          
          function handleMouseOutAdjThree(d, i) {
            d3.select(this)
              .classed("word-hovered", false)
              .interrupt(`mouseover-${adjTextThree}`)
                .attr("font-size", adjThreeSize);
          }
          
          function handleClick(d, i) {
            var e = d3.select(this);
            console.log(e._groups[0][0].id)
            e.classed("word-selected", !e.classed("word-selected"));
          }
        }
      });

    wordCloudSVG.start();
}

$(document).ready(prepareWordCloud);
// $(window).resize(resize);