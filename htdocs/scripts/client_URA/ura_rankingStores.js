let storeIDandNameDict = {};
async function getRanking(subzone_ID){
    let response   = await makeRequest(hostname + "/stores/road/"+subzone_ID, "GET", "");
    let storeData = JSON.parse(response).data;

    // storeData is already returned sorted but just to be sure, we sort the beta_score again.
    storeData.sort(function(a,b){
        return b.beta_score - a.beta_score;
    });

    storesRanked = document.getElementById("storesRanked");
    //Clear all the ranks each time this function is called
    storesRanked.innerHTML = "";

    rank = 0;
    for (x in storeData){
        storeIDandNameDict[storeData[x].store_id] = storeData[x].store_name;
        rank ++;
        // console.log(storeData)
        storesRanked.innerHTML += 
        `<a id="${storeData[x].store_id}" onclick="showStoreSpecificDetails(this,${storeData[x].store_id})" class="list-group-item pointer storesList reviewBodyFont">${rank+'. '}${storeData[x].store_name}</a>`;
    }
}

function showStoreSpecificDetails(e,storeId){
    let storesList = document.querySelectorAll('.storesList');

    document.getElementById("wordCloudNotEnoughWordsWarning").innerHTML     = "";
    document.getElementById("wordCloudNotEnoughWordsWarning").style.display = "none";

    if(e.classList.contains("active")){
        e.classList.remove("active");
        isCallForSubZone = true;
    
        dataPrepOnPageLoad(subzoneReviews);
        prepareSentimentOverTime(subzoneReviews,true)
        prepareWordCloud(subZoneNounAdjPairs)
      
    }else{
        isCallForSubZone = false;
        storesList.forEach(function(elem) {
            elem.classList.remove("active");
        });

        let sotURL = hostname + "/reviews/store/" + storeId;
        retrieveSOTbyStore(sotURL, "GET", "")
        
        let wcURL = hostname + "/adj_noun_pairs/store/" + storeId;
        retrieveWordCloudNounAdjPairs(wcURL, "GET", "")
        e.classList.add("active");
    }    
}