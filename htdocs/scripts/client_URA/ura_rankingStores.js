let storeIDandNameDict = {};
async function getRanking(subzone_ID){
    let response   = await makeRequest(hostname + "/stores/road/"+subzone_ID, "GET", "");
    let storeData = JSON.parse(response).data;

    // storeData is already returned sorted but just to be sure, we sort the beta_score again.
    storeData.sort(function(a,b){
        return b.beta_score - a.beta_score;
    });

    storesRanked = document.getElementById("accordion");
    //Clear all the ranks each time this function is called
    storesRanked.innerHTML = "";

    rank = 0;
    for (x in storeData){
        storeIDandNameDict[storeData[x].store_id] = storeData[x].store_name;
        rank ++;

        storeID = storeData[x].store_id
    
        // console.log(storeData)
        storesRanked.innerHTML += 
        `<div class="card">
            <div 
                onclick="showStoreSpecificDetails(this,${storeID})" 
                data-toggle="collapse" 
                data-target="#Store${storeID}" 
                class="card-header pointer reviewBodyFont storesList" 
                aria-expanded="false">
                ${rank+'. '}${storeData[x].store_name}
            </div>
        </div>
    
        <div id="Store${storeID}" class="collapse" data-parent="#accordion">
            <div class="card-body border d-flex justify-content-between ">
                <span id="rankOverallSentimentOverTime${storeID}"></span> 
                <span id="rankTotalNumberOfReviews${storeID}" class="reviewBodyFont"></span>
            </div>
        </div>
        `;

        displayStars(storeData[x].average_compound,storeID)
        document.getElementById("rankTotalNumberOfReviews"+storeID).textContent = storeData[x].num_of_reviews + "Reviews";
    }
    document.getElementById("accordion").style.display            = "block";
    document.getElementById("StorerRankedSpinner").style.display  = "none";
}
// `<a id="${storeData[x].store_id}" onclick="showStoreSpecificDetails(this,${storeData[x].store_id})" class="list-group-item pointer storesList reviewBodyFont">${rank+'. '}${storeData[x].store_name}</a>
// `

function showStoreSpecificDetails(e,storeId){
    let storesList = document.querySelectorAll('.storesList');

    document.getElementById("wordCloudNotEnoughWordsWarning").innerHTML     = "";
    document.getElementById("wordCloudNotEnoughWordsWarning").style.display = "none";

    if(e.classList.contains("card-active")){
        e.classList.remove("card-active");
        isCallForSubZone = true;
    
        dataPrepOnPageLoad(subzoneReviews, false);
        prepareSentimentOverTime(subzoneReviews,true)
        prepareWordCloud(subZoneNounAdjPairs)
      
    }else{
        isCallForSubZone = false;
        storesList.forEach(function(elem) {
            elem.classList.remove("card-active");
        });

        let sotURL = hostname + "/reviews/store/" + storeId;
        retrieveSOTbyStore(sotURL, "GET", "")
        
        let wcURL = hostname + "/adj_noun_pairs/store/" + storeId;
        retrieveWordCloudNounAdjPairs(wcURL, "GET", "")
        e.classList.add("card-active");
    }    
}