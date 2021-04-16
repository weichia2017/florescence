let storeIDandNameDict = {};
async function getRanking(subzone_ID, subzoneChoice){
    let response   = await makeRequest(hostname + "/stores/road/"+subzone_ID, "GET", "");
    let storeData = JSON.parse(response).data;

    // storeData is already returned sorted but just to be sure, we sort the beta_score again.
    storeData.sort(function(a,b){
        return b.beta_score - a.beta_score;
    });

    let storesRanked = document.getElementById("storeRanks"+subzoneChoice);
    //Clear all the ranks each time this function is called
    storesRanked.innerHTML = "";

    rank = 0;
    for (x in storeData){
        storeIDandNameDict[storeData[x].store_id] = storeData[x].store_name;
        rank ++;

        storeID   = storeData[x].store_id;
        storeName = storeData[x].store_name;

    
        // console.log(storeData)
        storesRanked.innerHTML += 
        `<div class="card">
            <div 
                onclick="showStoreSpecificDetails(this,${storeID},'${subzoneChoice}')" 
                data-toggle="collapse" 
                data-target="#Store${storeID}" 
                class="card-header pointer reviewBodyFont storesList${subzoneChoice}" 
                aria-expanded="false">
                ${rank+'. '}${storeName}
                <input type="hidden" value="${storeData[x].num_of_reviews}">
            </div>
            
        </div>
    
        <div id="Store${storeID}" class="collapse" data-parent="#storeRanks${subzoneChoice}">
            <div class="card-body border d-flex justify-content-between ">
                <span id="${subzoneChoice}RankOverallSentimentOverTime${storeID}"></span> 
                <span id="${subzoneChoice}RankTotalNumberOfReviews${storeID}" class="reviewBodyFont"></span>
            </div>
        </div>
        `;

        displayStars(storeData[x].average_compound,storeID,subzoneChoice)
        document.getElementById(subzoneChoice+"RankTotalNumberOfReviews"+storeID).textContent = storeData[x].num_of_reviews + "Reviews";
    }
    document.getElementById("storeRanks" +subzoneChoice).style.display          = "block";
    document.getElementById("storeRanksSpinner" + subzoneChoice).style.display  = "none";
}
// `<a id="${storeData[x].store_id}" onclick="showStoreSpecificDetails(this,${storeData[x].store_id})" class="list-group-item pointer storesList reviewBodyFont">${rank+'. '}${storeData[x].store_name}</a>
// `

function showStoreSpecificDetails(e,storeId,subzoneChoice){
    let storesList = document.querySelectorAll('.storesList'+subzoneChoice);

    document.getElementById("wordCloudNotEnoughWordsWarning"+subzoneChoice).innerHTML     = "";
    document.getElementById("wordCloudNotEnoughWordsWarning"+subzoneChoice).style.display = "none";

    //IF SELECTED UNSELECT
    if(e.classList.contains("card-active")){
        e.classList.remove("card-active");
        isCallForSubZone = true;

        //Remove store names besides the headers of SOT,WordCloud and Reviews Container
        document.querySelectorAll('.showStoreName'+subzoneChoice).forEach(function(elem){
            elem.innerHTML = "";
        });

        //Show Subzone Name 
        document.querySelectorAll('.subzoneNameContainer'+subzoneChoice).forEach(function(elem){
            elem.style.display = "block";
        });

        if(e.childNodes[1].value <= limitToShowIndividualStoreInsights){
            document.getElementById("entireSentimentOverTimeContainer"+subzoneChoice).style.display  = "block";
            document.getElementById("entireWordCloudContainer"+subzoneChoice).style.display          = "block";
            document.getElementById("unableToShowInsightsContainer"+subzoneChoice).style.display     = "none";
        }
        dataPrepOnPageLoad(window["reviews"+subzoneChoice], false,subzoneChoice);
        prepareSentimentOverTime(window["reviews"+subzoneChoice],subzoneChoice);
        prepareWordCloud(window["nounAdjPairs"+subzoneChoice],false,subzoneChoice);
      
    }
    //IF UNSELECTED SELECT
    else{
        isCallForSubZone = false;
        let noOfReviews = e.childNodes[1].value;

        storesList.forEach(function(elem) {
            elem.classList.remove("card-active");
        });

        let storeName = ''
        //If no number means is for single mode, if single mode no need to show rank number
        if( /\d/.test(subzoneChoice) ){
            //COMPARE MODE
            storeName = e.textContent.trim();
            //Update store names besides the headers of SOT,WordCloud and Reviews Container (WITH ICON)
            document.querySelectorAll('.showStoreName'+subzoneChoice).forEach(function(elem){
                elem.innerHTML = 
                `<span style="font-size:25px" class="material-icons">
                    store
                </span>  
                ${storeName}
                `;
            });
        }else{
            //SINGLE MODE
            storeName = e.textContent.trim().substring(3);
             //Update store names besides the headers of SOT,WordCloud and Reviews Container (WITHOUT ICON)
             document.querySelectorAll('.showStoreName'+subzoneChoice).forEach(function(elem){
                elem.innerHTML = storeName;
            });
        }

        //Hide Subzone Name
        document.querySelectorAll('.subzoneNameContainer'+subzoneChoice).forEach(function(elem){
            elem.style.display = "none";
        });
        
        // Only if more than limitNo of reviews
        if(noOfReviews > limitToShowIndividualStoreInsights){
            // WordCloud
            let wcURL = hostname + "/adj_noun_pairs/store/" + storeId;
            retrieveWordCloudNounAdjPairs(wcURL, "GET", "",subzoneChoice)
            document.getElementById("entireSentimentOverTimeContainer"+subzoneChoice).style.display  = "block";
            document.getElementById("entireWordCloudContainer"+subzoneChoice).style.display          = "block";
            document.getElementById("unableToShowInsightsContainer"+subzoneChoice).style.display     = "none";
            document.getElementById('wordCloudHeader').style.display                                 = 'block';
        }else{

            //
            document.querySelectorAll('.noOfReviewsForErrorInsights'+subzoneChoice).forEach(function(elem){
                elem.innerText = noOfReviews;
            });

            document.getElementById("errorInsightsSpinner"+subzoneChoice).style.display              = "block";
            document.getElementById("showReviewsContainerForErrorInsights"+subzoneChoice).innerHTML  = "";
            document.getElementById("shopNameForErrorInsights"+subzoneChoice).textContent            = storeName;
            document.getElementById("limitForErrorInsights").textContent  = limitToShowIndividualStoreInsights;

            document.getElementById("entireSentimentOverTimeContainer"+subzoneChoice).style.display  = "none";
            document.getElementById("entireWordCloudContainer"+subzoneChoice).style.display          = "none";
            document.getElementById("wordCloudReviewsContainer"+subzoneChoice).style.display         = "none";
            document.getElementById("unableToShowInsightsContainer"+subzoneChoice).style.display     = "block";

            //If both containers none hide the header for reviewsContainer
            if(document.getElementById("wordCloudReviewsContainerSubzone1").style.display == "none" &&
            document.getElementById("wordCloudReviewsContainerSubzone2").style.display == "none" ){
                document.getElementById('reviewsContainer').style.display = 'none';
            }

            if(document.getElementById("entireWordCloudContainerSubzone1").style.display == "none" &&
            document.getElementById("entireWordCloudContainerSubzone2").style.display == "none" ){
                document.getElementById('wordCloudHeader').style.display = 'none';
            }
        }

        
        let sotURL = hostname + "/reviews/store/" + storeId;
        retrieveSOTbyStore(sotURL, "GET", "",subzoneChoice)
        e.classList.add("card-active");
     
    }    
}