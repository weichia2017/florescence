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
                    setimentOverTimePrepared[y].PosR.push({review_id      : response[x].review_id,
                                                           review_text    : response[x].review_text,
                                                           review_date    : response[x].review_date,
                                                           compound_score : response[x].compound_score});
                } 
                else if(response[x].compound_score <= -0.05){
                    setimentOverTimePrepared[y].Neg += 1;
                    setimentOverTimePrepared[y].NegR.push({review_id      : response[x].review_id,
                                                           review_text    : response[x].review_text,
                                                           review_date    : response[x].review_date,
                                                           compound_score : response[x].compound_score});
                }
                else{
                    setimentOverTimePrepared[y].Neu += 1;
                    setimentOverTimePrepared[y].NeuR.push({review_id      : response[x].review_id,
                                                           review_text    : response[x].review_text,
                                                           review_date    : response[x].review_date,
                                                           compound_score : response[x].compound_score});
                }
            }
        }
    
    }
    // console.log(JSON.stringify(setimentOverTimePrepared)); 
    // console.log(setimentOverTimePrepared)

    drawSentimentOverTimeStackedBarChart(setimentOverTimePrepared);
}

// Making the SOT globally available 
var sentimentOverTimeChartsentimentOverTimeChartSVG;
function drawSentimentOverTimeStackedBarChart(data){

    var legendClassArray = []; //store legend classes to select bars in plotSingle()

    let w = document.getElementById('sentimentOverTimeContainerDiv').offsetWidth;
    let h = document.getElementById('sentimentOverTimeContainerDiv').offsetHeight;


    _chart(data,w,h)

    $(window).resize(_resizeSentimentOverTime);

    /* 
    *   Each time the window gets resized, 
    *   1. get the new width and height of the container
    *   2. remove inner HTML of sentiment over time chart
    *   3. draw a new sentiment over time chart
    */ 
    function _resizeSentimentOverTime(){
        w = document.getElementById('sentimentOverTimeContainerDiv').offsetWidth;
        h = document.getElementById('sentimentOverTimeContainerDiv').offsetHeight;

        if($(window).width() != width || $(window).height() != height){
            document.getElementById("sentimentOverTimeContainer").innerHTML = "";
            _chart(data,w,h);
        }
    }

    function _displayReviewsBelowSentimentOverTime(chosenReviews){
        document.getElementById("sentimentOverTimeClickedReviews").innerHTML = '';
        document.getElementById("sentimentReviewsContainer").style.display = "block";

        // Hide the reviews being shown under the wordcloud as well 
        document.getElementById("wordCloudClickedReviews").innerHTML = '';
        document.getElementById("wordCloudReviewsContainer").style.display = "none";

        // window.scrollBy(0, 500);
        document.getElementById('sentimentReviewsContainer').scrollIntoView({block: "end",behavior:'smooth'});


        chosenReviews.sort(function(a,b){
            return new Date(b.review_date) - new Date(a.review_date);
        });

        for (x in chosenReviews){
            let formattedDate = new Date(chosenReviews[x].review_date);
            // console.log(formattedDate.toLocaleFormat('%d-%b-%Y'))
            document.getElementById("sentimentOverTimeClickedReviews").innerHTML +=
                `<div class="card mr-3 ml-3 mt-2">
                    <div class="card-body">
                    <h6 class="reviewHeaderFont">Review Date: ${formattedDate.toLocaleDateString()}</h6>
                    <p class="reviewBodyFont">${chosenReviews[x].review_text}</p>
                    </div>
                </div>`;
        }
    }

    function _chart(csv,w,h) {
        var keys = ["Pos", "Neg", "Neu"]
        // console.log(keys)

        var year   = [...new Set(csv.map(d => d.Year))]
        var Months = [...new Set(csv.map(d => d.Month))]

        var options = d3.select("#year").selectAll("option")
            .data(year)
        .enter().append("option")
            .text(d => d)

        sentimentOverTimeChartSVG = d3.select("#sentimentOverTimeContainer").attr("viewBox", `0 0 ${w} ${h}`),
            margin = {top: 35, left: 35, bottom: 0, right: 20},
            width = +w - margin.left - margin.right,
            height = +h - margin.top - margin.bottom;

        var x = d3.scaleBand()
            .range([margin.left, width - margin.right])
            .padding(0.1)

        var y = d3.scaleLinear()
            .rangeRound([height - margin.bottom, margin.top])

        var xAxis = sentimentOverTimeChartSVG.append("g")
            .attr("transform", `translate(0,${height - margin.bottom})`)
            .attr("class", "x-axis")


        var yAxis = sentimentOverTimeChartSVG.append("g")
            .attr("transform", `translate(${margin.left},0)`)
            .attr("class", "y-axis")

        var z = d3.scaleOrdinal()
            .range([posColor, negColor, neuColor])
            .domain(keys);

        update(d3.select("#year").property("value"), 0)

        function update(input, speed) {

            var data = csv.filter(f => f.Year == input)

            data.forEach(function(d) {
                d.total = d3.sum(keys, k => +d[k])
                return d
            })

            // As identified values 7 and below produce floats, thus if value is below 8 we set max to be 8
            max = d3.max(data, d => d3.sum(keys, k => +d[k]));
            max = max < 8 ? 8 : max;
            y.domain([0, max]);
            
            sentimentOverTimeChartSVG.selectAll(".y-axis").transition().duration(speed)
                .call(d3.axisLeft(y).ticks(null,'s'))

            data.sort(d3.select("#sort").property("checked")
                ? (a, b) => b.total - a.total
                : (a, b) => Months.indexOf(a.Month) - Months.indexOf(b.Month))

            x.domain(data.map(d => d.Month));

            sentimentOverTimeChartSVG.selectAll(".x-axis").transition().duration(speed)
                .call(d3.axisBottom(x).tickSizeOuter(0))

            var group = sentimentOverTimeChartSVG.selectAll("g.layer")
                .data(d3.stack().keys(keys)(data), d => d.key)

            group.exit().remove()

            group.enter().append("g")
                .classed("layer", true)
                .attr("fill", d => z(d.key));


            var bars = sentimentOverTimeChartSVG.selectAll("g.layer").selectAll("rect")
                .data(d => d, e => e.data.Month);

            bars.exit().remove()

            console.log(bars)

            bars
            .enter()
            .append("rect")
                .attr("width", x.bandwidth())
                .merge(bars)
            .transition().duration(speed)
                .attr("x", d => x(d.data.Month))
                .attr("id",d => y(d[1]) + x(d.data.Month))
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


            var tooltip = sentimentOverTimeChartSVG.append("g")
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
            sentimentOverTimeChartSVG.select(".ToolTip").remove();


            let rectID = d3.select(this)._groups[0][0].id
            
            if(sentimentOverTimeSelected[0] != rectID){
                d3.select(this).attr("stroke","pink").attr("stroke-width",0.2);

            }
        }

        var sentimentOverTimeSelected = []
        function showReviews(d,i){
            //Clear all strokes for all rect
            bars.enter()
                .selectAll("rect")
                .attr("stroke","pink")
                .attr("stroke-width","0.2");

            let rectID = d3.select(this)._groups[0][0].id
        
           
            // If sentimentOverTimeSelected contains the same rectID remove this rectID from container
            if(sentimentOverTimeSelected[0] == rectID){
                sentimentOverTimeSelected.pop();
                
                // Close and empty the reviews container
                document.getElementById('sentimentOverTimeClickedReviews').innerHTML = '';
                document.getElementById('sentimentReviewsContainer').style.display = 'none';
            }
            // Update the reviews container with the newly selected reviews
            else{
                // Add stroke for just this selected rect
                d3.select(this)
                .attr("stroke","black")
                .attr("stroke-width",1.5)
                .style("cursor", "pointer");

                 // If container is empty just add rectID
                if(sentimentOverTimeSelected.length == 0){
                    sentimentOverTimeSelected.push(rectID);
                } 
                // Clear container and add the new rectID into the container
                else{
                    sentimentOverTimeSelected.pop();
                    sentimentOverTimeSelected.push(rectID)
                }

                let selectedSentiment = ''
                sentimentSelected = d3.select(this)._groups[0][0].parentNode.attributes[1].value;
                if(sentimentSelected == posColor){
                    selectedSentiment = "PosR"
                }else if(sentimentSelected == neuColor){
                    selectedSentiment = "NeuR"
                }else{
                    selectedSentiment = "NegR"
                }
                _displayReviewsBelowSentimentOverTime(d.data[selectedSentiment])
            }
        }

        // function getKeyByValue(object, value) {
        //   return Object.keys(object).find(key => object[key] === value);
        // }

        // Total Reviews on top of the bar text
        var text = sentimentOverTimeChartSVG.selectAll(".text")
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

        var legend = sentimentOverTimeChartSVG.selectAll(".legend")
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
