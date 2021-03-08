function sentimentOverTimePrepareData(response){

    // console.log(response)
    let setimentOverTimePrepared = [];
    let years = [];
    for (x in response){
        year = new Date(response[x].review_date).getFullYear();
        years.push(year);
    }

    let yearsUnique = Array.from(new Set(years));
    yearsUnique.sort().reverse();
    let months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    
    // console.log(yearsUnique)
    // Create All the empty Values for all the months of all the years that exist in the response
    for (x in yearsUnique){
        for (y in months){
            let posR          = [];
            let negR          = [];
            let neuR          = [];
    
            let pos           = 0;
            let neg           = 0;
            let neu           = 0;

            setimentOverTimePrepared.push({ Year: yearsUnique[x],
                                     Month: months[y],
                                     Pos: pos,
                                     Neg: neg,
                                     Neu: neu,
                                     PosR: posR,
                                     NegR: negR,
                                     NeuR: neuR})                          
        }
    }

    for (x in response){
        year = new Date(response[x].review_date).getFullYear();
        month = new Date(response[x].review_date).toLocaleString('default', { month: 'long' });
    
        for(y in setimentOverTimePrepared){

            if(setimentOverTimePrepared[y].Year == year &&
                month.includes(setimentOverTimePrepared[y].Month)){
                if(response[x].compound_score >= 0.05){
                    setimentOverTimePrepared[y].Pos += 1;
                    setimentOverTimePrepared[y].PosR.push({review_id     : response[x].review_id,
                                                            review_text  : response[x].review_text});
                } 
                else if(response[x].compound_score <= -0.05){
                    setimentOverTimePrepared[y].Neg += 1;
                    setimentOverTimePrepared[y].NegR.push({review_id    : response[x].review_id,
                                                            review_text : response[x].review_text});
                }
                else{
                    setimentOverTimePrepared[y].Neu += 1;
                    setimentOverTimePrepared[y].NeuR.push({review_id    : response[x].review_id,
                                                            review_text : response[x].review_text});
                }
            }
        }
    
    }
    // console.log(JSON.stringify(setimentOverTimePrepared)); 

    drawSentimentOverTimeStackedBarChart(setimentOverTimePrepared);
}

function drawSentimentOverTimeStackedBarChart(data){

var legendClassArray = []; //store legend classes to select bars in plotSingle()

let w = document.getElementById('test').offsetWidth;
let h = 400;

console.log(document.getElementById('test').offsetWidth);
console.log(h)

chart(data,w,h)

$(window).resize(resizeSentimentOverTime);

/* 
*   Each time the window gets resized, 
*   1. get the new width and height of the container
*   2. remove inner HTML of word cloud
*   3. draw a new wordcloud
*/ 
function resizeSentimentOverTime(){
    w = document.getElementById('test').offsetWidth;
    h = 400;

    if($(window).width() != width || $(window).height() != height){
        removeSentimentOverTimeChart();
        chart(data,w,h);
    }
}

function removeSentimentOverTimeChart(){
    document.getElementById("sentimentOverTimeContainer").innerHTML = "";
}


function chart(csv,w,h) {
	var keys = ["Pos", "Neg", "Neu"]
    console.log(keys)

	var year   = [...new Set(csv.map(d => d.Year))]
	var Months = [...new Set(csv.map(d => d.Month))]

	var options = d3.select("#year").selectAll("option")
		.data(year)
	.enter().append("option")
		.text(d => d)

	var svg = d3.select("#sentimentOverTimeContainer").attr("viewBox", `0 0 ${w} ${h}`),
		margin = {top: 35, left: 35, bottom: 0, right: 20},
		width = +w - margin.left - margin.right,
		height = +h - margin.top - margin.bottom;

	var x = d3.scaleBand()
		.range([margin.left, width - margin.right])
		.padding(0.1)

	var y = d3.scaleLinear()
		.rangeRound([height - margin.bottom, margin.top])

	var xAxis = svg.append("g")
		.attr("transform", `translate(0,${height - margin.bottom})`)
		.attr("class", "x-axis")


	var yAxis = svg.append("g")
		.attr("transform", `translate(${margin.left},0)`)
		.attr("class", "y-axis")

	var z = d3.scaleOrdinal()
		.range(["#79a925", "#f32c22", "#99bebe"])
		.domain(keys);

	update(d3.select("#year").property("value"), 0)

	function update(input, speed) {

		var data = csv.filter(f => f.Year == input)

		data.forEach(function(d) {
			d.total = d3.sum(keys, k => +d[k])
			return d
		})

		y.domain([0, d3.max(data, d => d3.sum(keys, k => +d[k]))]).nice();

        
		svg.selectAll(".y-axis").transition().duration(speed)
			.call(d3.axisLeft(y).ticks(null, "s"))


		data.sort(d3.select("#sort").property("checked")
			? (a, b) => b.total - a.total
			: (a, b) => Months.indexOf(a.Month) - Months.indexOf(b.Month))

		x.domain(data.map(d => d.Month));

		svg.selectAll(".x-axis").transition().duration(speed)
			.call(d3.axisBottom(x).tickSizeOuter(0))

		var group = svg.selectAll("g.layer")
			.data(d3.stack().keys(keys)(data), d => d.key)

		group.exit().remove()

		group.enter().append("g")
			.classed("layer", true)
			.attr("fill", d => z(d.key));


		var bars = svg.selectAll("g.layer").selectAll("rect")
			.data(d => d, e => e.data.Month);

		bars.exit().remove()

		bars
    .enter()
    .append("rect")
			.attr("width", x.bandwidth())
			.merge(bars)
		.transition().duration(speed)
			.attr("x", d => x(d.data.Month))
			.attr("y", d => y(d[1]))
			.attr("height", d => y(d[0]) - y(d[1]))
      
    bars.enter().selectAll("rect")
      .on("mouseover", showToolTip)
      .on("mouseout", hideToolTip)
      .on('click', showReviews)

    function showToolTip(d, i) {
      var xPos = parseFloat(d3.select(this).attr("x"));
      var yPos = parseFloat(d3.select(this).attr("y"));
      var height = parseFloat(d3.select(this).attr("height"));
      var width = parseFloat(d3.select(this).attr("width"))

      d3.select(this)
      .attr("stroke","black")
      .attr("stroke-width",1)
      .style("cursor", "pointer");


      var tooltip = svg.append("g")
        .attr("class", "ToolTip")
            
        tooltip.append("rect")
        .attr("width", 120)
        .attr("height", 30)
        .attr("x",xPos +width+5)
        .attr("y",yPos +height/2-15)
        .attr("fill", "black")
        .style("opacity", 0.75);

        tooltip.append("text")
        .attr("x",xPos +width+10)
        .attr("y",yPos +height/2+8)
        .attr("fill","white")
        .text(d[1]-d[0] + " Review(s)");
    }
          
    function hideToolTip(d, i) {
      svg.select(".ToolTip").remove();
      d3.select(this).attr("stroke","pink").attr("stroke-width",0.2);
    }

    function showReviews(d,i){
      console.log(d.data)
      console.log(d[1]-d[0])
      let selected = getKeyByValue(d.data, (d[1]-d[0])) + "R"
      console.log(selected)
      console.log(d.data[selected])
    }

    function getKeyByValue(object, value) {
      return Object.keys(object).find(key => object[key] === value);
    }

    // Total Reviews on top of the bar text
	var text = svg.selectAll(".text")
			.data(data, d => d.Month);

		text.exit().remove()

		text.enter().append("text")
			.attr("class", "text")
			.attr("text-anchor", "middle")
			.merge(text)
		.transition().duration(speed)
			.attr("x", d => x(d.Month) + x.bandwidth() / 2)
			.attr("y", d => y(d.total) - 5)
            .attr("font-size","15px")
			.text(d => d.total)     
	}

    var legend = svg.selectAll(".legend")
        .data(z.domain().slice().reverse())
        .enter().append("g")
        //.attr("class", "legend")
        .attr("class", function (d) {
            legendClassArray.push(d.replace(/\s/g, '')); //remove spaces
            return "legend";
        })
        .attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; });

    //reverse order to match order in which bars are stacked    
    legendClassArray = legendClassArray.reverse();

    legend.append("rect")
        .attr("x", width -10)
        // .attr("y", 00)
        .attr("width", 18)
        .attr("height", 18)
        .style("fill", z)
        .attr("id", function (d, i) {
            return "id" + d.replace(/\s/g, '');
        })

    legend.append("text")
      .attr("x", width +10)
      .attr("y", 9)
      .attr("dy", ".35em")
      .style("text-anchor", "start")
      .text(function(d) { return d ; });

	var select = d3.select("#year")
		.on("change", function() {
			update(this.value, 750)
		})

	var checkbox = d3.select("#sort")
		.on("click", function() {
			update(select.property("value"), 750)
		})
    }
}
