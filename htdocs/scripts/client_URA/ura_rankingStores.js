let storeIDandNameDict = {};
async function getRanking(subzone_ID, IDtoPlaceRank, IDforRankSpinner){
    let response   = await makeRequest(hostname + "/stores/road/"+subzone_ID, "GET", "");
    let storeData = JSON.parse(response).data;

    // storeData is already returned sorted but just to be sure, we sort the beta_score again.
    storeData.sort(function(a,b){
        return b.beta_score - a.beta_score;
    });

    let subzoneChoice      = IDtoPlaceRank.substring(IDtoPlaceRank.length - 8);
    let storesRanked = document.getElementById(IDtoPlaceRank);
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
                class="card-header pointer reviewBodyFont storesList" 
                aria-expanded="false">
                ${rank+'. '}${storeName}
                <input type="hidden" value="${storeData[x].num_of_reviews}">
            </div>
            
        </div>
    
        <div id="Store${storeID}" class="collapse" data-parent="#${IDtoPlaceRank}">
            <div class="card-body border d-flex justify-content-between ">
                <span id="rankOverallSentimentOverTime${storeID}"></span> 
                <span id="${subzoneChoice}RankTotalNumberOfReviews${storeID}" class="reviewBodyFont"></span>
            </div>
        </div>
        `;

        displayStars(storeData[x].average_compound,storeID)
        document.getElementById(subzoneChoice+"RankTotalNumberOfReviews"+storeID).textContent = storeData[x].num_of_reviews + "Reviews";
    }
    document.getElementById(IDtoPlaceRank).style.display     = "block";
    document.getElementById(IDforRankSpinner).style.display  = "none";
}
// `<a id="${storeData[x].store_id}" onclick="showStoreSpecificDetails(this,${storeData[x].store_id})" class="list-group-item pointer storesList reviewBodyFont">${rank+'. '}${storeData[x].store_name}</a>
// `

function showStoreSpecificDetails(e,storeId,subZoneChoice){
    let storesList = document.querySelectorAll('.storesList');

    document.getElementById("wordCloudNotEnoughWordsWarning"+subZoneChoice).innerHTML     = "";
    document.getElementById("wordCloudNotEnoughWordsWarning"+subZoneChoice).style.display = "none";

    //IF SELECTED UNSELECT
    if(e.classList.contains("card-active")){
        e.classList.remove("card-active");
        isCallForSubZone = true;

        //Remove store names besides the headers of SOT,WordCloud and Reviews Container
        document.querySelectorAll('.showStoreName'+subZoneChoice).forEach(function(elem){
            elem.innerText = "";
        });

        if(e.childNodes[1].value <= limitToShowIndividualStoreInsights){
            document.getElementById("entireSentimentOverTimeContainer").style.display  = "block";
            document.getElementById("entireWordCloudContainer").style.display          = "block";
            document.getElementById("unableToShowInsightsContainer").style.display     = "none";
        }
        dataPrepOnPageLoad(subzoneReviews, false);
        prepareSentimentOverTime(subzoneReviews,subZoneChoice);
        prepareWordCloud(subZoneNounAdjPairs,false,subZoneChoice);
      
    }
    //IF UNSELECTED SELECT
    else{
        isCallForSubZone = false;
        let noOfReviews = e.childNodes[1].value;
        let storeName   = e.textContent.trim().substring(3);

        storesList.forEach(function(elem) {
            elem.classList.remove("card-active");
        });

        //Update store names besides the headers of SOT,WordCloud and Reviews Container
        document.querySelectorAll('.showStoreName'+subZoneChoice).forEach(function(elem){
            elem.innerText = storeName;
        });
        
        // Only of more than limitNo of reviews
        if(noOfReviews> limitToShowIndividualStoreInsights){
            // WordCloud
            let wcURL = hostname + "/adj_noun_pairs/store/" + storeId;
            retrieveWordCloudNounAdjPairs(wcURL, "GET", "",subZoneChoice)
            document.getElementById("entireSentimentOverTimeContainer").style.display  = "block";
            document.getElementById("entireWordCloudContainer").style.display          = "block";
            document.getElementById("unableToShowInsightsContainer").style.display     = "none";
        }else{

            //Update store names besides the headers of SOT,WordCloud and Reviews Container
            document.querySelectorAll('.noOfReviewsForErrorInsights').forEach(function(elem){
                elem.innerText = noOfReviews;
            });

            document.getElementById("errorInsightsSpinner").style.display              = "block";
            document.getElementById("showReviewsContainerForErrorInsights").innerHTML  = "";
            document.getElementById("shopNameForErrorInsights").textContent            = storeName;
            document.getElementById("limitForErrorInsights").textContent               = limitToShowIndividualStoreInsights;
            
            document.getElementById("entireSentimentOverTimeContainer").style.display  = "none";
            document.getElementById("entireWordCloudContainer").style.display          = "none";
            document.getElementById("wordCloudReviewsContainer"+subzoneChoice).style.display  = "none";
            document.getElementById("unableToShowInsightsContainer").style.display     = "block";
        }

        
        let sotURL = hostname + "/reviews/store/" + storeId;
        retrieveSOTbyStore(sotURL, "GET", "",subZoneChoice)
        e.classList.add("card-active");
     
    }    
}