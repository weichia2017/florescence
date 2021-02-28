// HardCoded Values
const myWords = [

    {
    "ADJ": ["good","great","expensive"], 
    "COUNT": 23, 
    "NOUN": "wine"
    },
    {
    "ADJ": ["same","long","last"], 
    "COUNT": 7, 
    "NOUN": "time"
    }, 
    {
    "ADJ": ["friendly","knowledgeable","expensive"], 
    "COUNT": 9, 
    "NOUN": "staff"
    }, 
    {
    "ADJ": ["great","excellent","good"], 
    "COUNT": 9, 
    "NOUN": "service"
    }, 
    {
    "ADJ": ["good","great","extensive"], 
    "COUNT": 14, 
    "NOUN": "selection"
    }, 
    {
    "ADJ": ["reasonable","good","steep"], 
    "COUNT": 6, 
    "NOUN": "price"
    }, 
    {
    "ADJ": ["great","good","cozy"], 
    "COUNT": 16, 
    "NOUN": "place"
    }, 
    {
    "ADJ": ["extensive","wide","uptodate"], 
    "COUNT": 7, 
    "NOUN": "list"
    }, 
    {
    "ADJ": ["good","great","delicious"], 
    "COUNT": 17, 
    "NOUN": "food"
    }, 
    {
    "ADJ": ["best","great","little"], 
    "COUNT": 6, 
    "NOUN": "bar"
    }
];

var w = 650
var h = 450

drawWordcLOUD(w,h)

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
    removeWordCloud();
    drawWordcLOUD(w,h);
}

function removeWordCloud(){
    document.getElementById("wordCloudContainer").innerHTML = ""
    // wordCloudContainer = d3.select("#wordCloudContainer");
    // wordCloudContainer.selectAll("*").remove()
}

function drawWordcLOUD(){
    // set the dimensions and margins of the graph
    var margin = {top: 5, right: 5, bottom: 5, left: 5},
    width = w - margin.left - margin.right,
    height = h - margin.top - margin.bottom;

    console.log(width)
    console.log(height)

    // append the svg object to the body of the page
    var svg = d3.select("#wordCloudContainer").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
    .append("g")
        .attr("transform",
            "translate(" + margin.left + "," + margin.top + ")");

    // Constructs a new cloud layout instance. It run an algorithm to find the position of words that suits your requirements
    // Wordcloud features that are different from one word to the other must be here
    var layout = d3.layout.cloud()
    .size([width, height])
    .words(myWords.map(function(d) { return {text: d.NOUN, size:d.COUNT}; }))
    .padding(10)        //space between words
    .rotate(0)         // To rotate -> function() { return ~~(Math.random() * 2) * 90; }
    .fontSize(function(d) { return  Math.log10(d.size)*60; })  // Originial is just d.size ...; Log Math.log10(d.size)*60; Initiall used Math.abs(d.size - average)/average * 60
    .on("end", draw);
    layout.start();

    // This function takes the output of 'layout' above and draw the words
    // Wordcloud features that are THE SAME from one word to the other can be here
    function draw(words) {
    svg
        // .append("g")
        .attr("transform", "translate(" + layout.size()[0] / 2 + "," + layout.size()[1] / 2 + ")")
        .selectAll("text")
        .data(words)
        .enter().append("text")
        .style("font-size", function(d) { return d.size; })  
        .style("fill", color => randomColor())
        .attr("text-anchor", "middle")
        .style("font-family", "Impact")
        .attr("transform", function(d) {
        return "translate(" + [d.x, d.y]  + ")rotate(" + d.rotate + ")";
        })
        .text(function(d) { return d.text; }) 
    }
}

function randomColor () {
    var chars = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++)
        color += chars[Math.floor(Math.random() * 16)];
    return color;
};

$(window).resize(resize)