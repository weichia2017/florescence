async function retrieveSOTbyStore(url,method,values){
    document.getElementById("sentimentOverTimeContainerDiv").style.display          = "none";
    // document.getElementById("overallSentimentScore").style.display                  = "none";
    // document.getElementById("totalNoOfReviewsContainer").style.display              = "none";
    
    document.getElementById("SOTSpinner").style.display                             = "block";
    // document.getElementById("overallSentimentScoreContainerSpinner").style.display  = "block";
    // document.getElementById("totalReviewsContainerSpinner").style.display           = "block";

    var response = await makeRequest(url, method, values);  
    let reviews  = JSON.parse(response).data;

    dataPrepOnPageLoad(reviews,false);
    prepareSentimentOverTime(reviews,false);
}

let setimentOverTimePrepared = [];
function prepareSentimentOverTime(reviews,isShowSpinner){
    if(!isShowSpinner){
        document.getElementById("sentimentOverTimeContainerDiv").style.display          = "block";
        document.getElementById("overallSentimentScore").style.display                  = "block";
        document.getElementById("totalNoOfReviewsContainer").style.display              = "block";
        
        document.getElementById("SOTSpinner").style.display                             = "none";
        document.getElementById("overallSentimentScoreContainerSpinner").style.display  = "none";
        document.getElementById("totalReviewsContainerSpinner").style.display           = "none"; 
    }

    // document.getElementById("overallSentimentScore").style.display      = "block";
    // document.getElementById("totalNoOfReviewsContainer").style.display  = "block";
    document.getElementById("sentimentOverTimeContainer").innerHTML     = "";
    document.getElementById("year").innerHTML                           = "";
    setimentOverTimePrepared = [];
    
    // console.log(reviews)
    let years = [];
    for (x in reviews){
        year = new Date(reviews[x].review_date).getFullYear();
        years.push(year);
    }

    let yearsUnique = Array.from(new Set(years));
    yearsUnique.sort().reverse();
    let months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    
    // console.log(yearsUnique)
    // Create All the empty Values for all the months of all the years that exist in the reviews
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

    for (x in reviews){
        year = new Date(reviews[x].review_date).getFullYear();
        month = new Date(reviews[x].review_date).toLocaleString('default', { month: 'long' });
    
        for(y in setimentOverTimePrepared){
            if(setimentOverTimePrepared[y].Year == year &&
                month.includes(setimentOverTimePrepared[y].Month)){
                if(reviews[x].compound_score >= 0.05){
                    setimentOverTimePrepared[y].Pos += 1;
                    setimentOverTimePrepared[y].PosR.push(reviews[x].review_id);
                } 
                else if(reviews[x].compound_score <= -0.05){
                    setimentOverTimePrepared[y].Neg += 1;
                    setimentOverTimePrepared[y].NegR.push(reviews[x].review_id);
                }
                else{
                    setimentOverTimePrepared[y].Neu += 1;
                    setimentOverTimePrepared[y].NeuR.push(reviews[x].review_id);
                }
            }

            // if(setimentOverTimePrepared[y].Year == year &&
            //     month.includes(setimentOverTimePrepared[y].Month)){
            //     if(reviews[x].compound_score >= 0.05){
            //         setimentOverTimePrepared[y].Pos += 1;
            //         setimentOverTimePrepared[y].PosR.push({review_id      : reviews[x].review_id,
            //                                                review_text    : reviews[x].review_text,
            //                                                review_date    : reviews[x].review_date,
            //                                                compound_score : reviews[x].compound_score,
            //                                                store_id       : reviews[x].store_id});
            //     } 
            //     else if(reviews[x].compound_score <= -0.05){
            //         setimentOverTimePrepared[y].Neg += 1;
            //         setimentOverTimePrepared[y].NegR.push({review_id      : reviews[x].review_id,
            //                                                review_text    : reviews[x].review_text,
            //                                                review_date    : reviews[x].review_date,
            //                                                compound_score : reviews[x].compound_score,
            //                                                store_id       : reviews[x].store_id});
            //     }
            //     else{
            //         setimentOverTimePrepared[y].Neu += 1;
            //         setimentOverTimePrepared[y].NeuR.push({review_id      : reviews[x].review_id,
            //                                                review_text    : reviews[x].review_text,
            //                                                review_date    : reviews[x].review_date,
            //                                                compound_score : reviews[x].compound_score,
            //                                                store_id       : reviews[x].store_id});
            //     }
            // }
        }
    
    }
    // console.log(JSON.stringify(setimentOverTimePrepared)); 

    let w = document.getElementById('sentimentOverTimeContainerDiv').offsetWidth;
    let h = 400;
    drawSentimentOverTimeStackedBarChart(w,h);
}


function updateWordCloud(chosenReviews,selectedSentiment){
    document.getElementById("wordCloudClickedReviews").innerHTML = '';
    document.getElementById("wordCloudReviewsContainer").style.display = "none";
    
    if(chosenReviews.length > 10){
        document.getElementById("wordCloudNotEnoughWordsWarning").style.display = "none";
        document.getElementById("wordCloudContainer").style.display             = "block";
        document.getElementById('wordCloudContainer').scrollIntoView({block: "end",behavior:'smooth'});
        
        let dataToBeSentToServer = [{data:[]}];
        dataToBeSentToServer[0].data = chosenReviews
        let url = hostname + "/adj_noun_pairs/";
        retrieveWordCloudNounAdjPairs(url,"POST",JSON.stringify(dataToBeSentToServer[0]))
      }
      else{
        selectedReview = chosenReviews //the main variable is in the first few lines of dashboard.php
        if(selectedSentiment == "NeuR"){
            selectedSentiment = "neutral"
        }else if(selectedSentiment == "NegR"){
            selectedSentiment = "negative"
        }else{
            selectedSentiment = "positive"
        }

        document.getElementById("wordCloudContainer").style.display          = "none";
        document.getElementById("wordCloudNotEnoughWordsWarning").innerHTML  = 
        `<div class='p-5 text-center' style='position: absolute;top: 30%;width:100%'>
         <div style="font-size:50px; color: #fdcc0d;" class="material-icons text-center">
                warning_amber  
            </div>
         <br>
         Not enough reviews to display wordcloud. 
         Click <a href="javascript:void(0)" onclick="displayReviewsBelowWordCloud_BelowTenReviews(selectedReview)">here</a>
           to view the ${chosenReviews.length} ${selectedSentiment} review(s) instead
        </div>`
        document.getElementById("wordCloudNotEnoughWordsWarning").style.display = "block";
        document.getElementById('wordCloudNotEnoughWordsWarning').scrollIntoView({block: "end",behavior:'smooth'});
      }



   

    // chosenReviews.sort(function(a,b){
    //     return new Date(b.review_date) - new Date(a.review_date);
    // });

    
    // for (x in chosenReviews){
    //     let formattedDate = new Date(chosenReviews[x].review_date);
    //     // console.log(formattedDate.toLocaleFormat('%d-%b-%Y'))
    //     document.getElementById("sentimentOverTimeClickedReviews").innerHTML +=
    //         `<div class="card mr-3 ml-3 mt-2">
    //             <div class="card-body">
    //             <h6 class="reviewBodyFont" style="font-size:20px">
    //                 <span style="font-size:25px; color: rgb(92, 92, 92)" class="material-icons float-left">
    //                 store
    //                 </span>
    //                 <div class="float-left ml-1" >
    //                 ${storeIDandNameDict[chosenReviews[x].store_id]}
    //                 </div> 
    //             </h6>
    //             <br>
    //             <h6 class="reviewHeaderFont">Review Date: ${formattedDate.toLocaleDateString()}</h6>
    //             <p class="reviewBodyFont">${chosenReviews[x].review_text}</p>
    //             </div>
    //         </div>`;
    // }
}

/* 
*   Each time the window gets resized, 
*   1. get the new width and height of the container
*   2. remove inner HTML of sentiment over time chart
*   3. draw a new sentiment over time chart
*/ 
function resizeSentimentOverTime(){
    let w = document.getElementById('sentimentOverTimeContainerDiv').offsetWidth;
    let h = 400;

    if($(window).width() != width || $(window).height() != height){
        document.getElementById("sentimentOverTimeContainer").innerHTML = "";
        drawSentimentOverTimeStackedBarChart(w,h);
    }
}

function drawSentimentOverTimeStackedBarChart(w,h) {
    var legendClassArray = []; //store legend classes to select bars in plotSingle()

	var keys = ["Pos", "Neg", "Neu"]
    // console.log(keys)

	var year   = [...new Set(setimentOverTimePrepared.map(d => d.Year))]
	var Months = [...new Set(setimentOverTimePrepared.map(d => d.Month))]

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
        .range([posColor, negColor, neuColor])
        .domain(keys);

	update(d3.select("#year").property("value"), 0)

	function update(input, speed) {
		var data = setimentOverTimePrepared.filter(f => f.Year == input)

		data.forEach(function(d) {
			d.total = d3.sum(keys, k => +d[k])
			return d
		})

        // As identified values 7 and below produce floats, thus if value is below 8 we set max to be 8
        max = d3.max(data, d => d3.sum(keys, k => +d[k]));
        max = max < 8 ? 8 : max;
		y.domain([0, max]);
        
		svg.selectAll(".y-axis").transition().duration(speed)
			.call(d3.axisLeft(y).ticks(null,'s'))

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
      .on('click', prepWordCloud)

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

    function prepWordCloud(d,i){
      
        let selectedSentiment = ''
        sentimentSelected = d3.select(this)._groups[0][0].parentNode.attributes[1].value;
        if(sentimentSelected == posColor){
            selectedSentiment = "PosR"
        }else if(sentimentSelected == neuColor){
            selectedSentiment = "NeuR"
        }else{
            selectedSentiment = "NegR"
        }
 
        updateWordCloud(d.data[selectedSentiment],selectedSentiment)
    }

    // function getKeyByValue(object, value) {
    //   return Object.keys(object).find(key => object[key] === value);
    // }

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

$(window).resize(resizeSentimentOverTime());