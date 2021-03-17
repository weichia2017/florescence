<?php  
$storeID = '1';
if( isset($_GET['storeID']) ){
  $storeID = $_GET['storeID'];
  if($storeID == '')
    $storeID = '1';
}
?>
<!Doctype html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Company's Name Dashboard</title>

    <!-- Fontwesome -->
    <!-- <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/> -->

    <!-- Material Design (External) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS (External) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- Load d3.js (External) -->
    <script src="https://d3js.org/d3.v4.min.js"></script>

    <!-- Load Anton font from google fonts (External) -->
    <!-- https://fonts.google.com/specimen/Anton?preview.text_type=custom&sidebar.open=true&selection.family=Anton -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">

    <!-- Load d3-cloud from scripts folder -->
    <script src="scripts/d3.layout.cloud.js"></script>

    <!-- Load wordcloud from scripts folder -->
    <script src="scripts/overallSentimentScore.js" defer></script>

    <!-- Load wordcloud from scripts folder -->
    <script src="scripts/wordCloud.js" defer></script>

    <!-- Load sentimentScore from scripts folder -->
    <script src="scripts/sentimentScoreDonut.js" defer></script>

    <!-- Load sentimentScoreOverTime from scripts folder -->
    <script src="scripts/sentimentOverTime.js" defer></script>

    <style>
      .white-bg{
        background-color: white;
      }

      .navbar-brand {
        padding-top: .75rem;
        padding-bottom: .75rem;
        font-size: 1rem;
        background-color: rgba(0, 0, 0, .25);
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
      }

      .navbar .navbar-toggler {
        top: .25rem;
        right: 1rem;
      }

      body{
        background-color: rgb(243, 243, 243);
      }

      .totalReviewValue{
        font-family:'Anton', sans-serif;
        color: rgb(92, 92, 92);
        font-size: 1.8em;
        float:left;
        margin-left:25px;
      }

      .stars{
        font-size:40px; 
        color: #fdcc0d;
      }

      #wordCloudContainer{
        border: 1px solid rgb(36, 36, 36);
        border-radius: 7px;
      }

      .word-default {
          fill: cadetblue;
          font-weight: normal;
        }
      .word-hovered {
          fill: teal;
          cursor: pointer;
          /* font-weight: bold; */
        }
      .word-selected {
          fill: darkslategrey;
          /* font-weight: bold; */
        }

      .donut-hovered{
        cursor: pointer;
      }
      .main-overlay {
        height: 100%;
        width: 100%;
        display: none;
        position: fixed;
        z-index: 2;
        top: 0;
        left: 0;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0, 0.9);
      }

      .spinner{
        position: absolute;
        top: 50%;
        left: 47%;
      }

      .wordCloudWhiteBackground{
        background-image: url("images/white-bg.png");
        /* background-size:cover;                   
        background-repeat: no-repeat; */
        height:400px;
      }
      .scrollReviews{
        height:400px;
        overflow-y: scroll;
      }
      .closeReviews{
        cursor: pointer;
      }
      .highLightedText{
        background-color: #FFFF00
      }
    }
    </style>
  </head>

  <body>
  <div id="myNav" class="main-overlay">
    <div class="spinner-border text-light spinner" role="status"> </div>
  </div>

  <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow" style="z-index: 1">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#"><span id="shopNameNavBar"></span></a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </header>

  <main class="container" style="z-index: 0">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Dashboard</h1>

      <!-- <button class="btn btn-primary" id="UpdateButton" onclick=test()>Update</button> -->
    </div>

    <!-- <form>
      <input type="text" name=storeID>
      <input type="submit">
    </form> -->
    <input type="hidden" value=<?= $storeID?>  id="getStoreID">

    <div class="container">
      <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <!-- <i style="color: rgb(92, 92, 92)" class="fas fa-user-edit fa-lg"></i> -->
            <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
              rate_review
            </span>
            <!-- Create a div where the total number of reviews will be -->
            <div class="float-left ml-1" >Total Reviews:</div> 
            <div class="container totalReviewValue" id="totalNoOfReviewsContainer"></div>
          </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              auto_graph
            </span>
            <div class="float-left ml-1" >Overall Sentiment Score:</div> 
            <div class="container float-left ml-3" id="overallSentimentScore"></div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-5 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow block pl-1" >
          <div class="lead">
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              tag_faces
            </span>
            <div class="float-left ml-1" >Sentiment Score:</div> 
            <!-- Create a div where the sentimentScore will be -->
            <div class="container float-left" id="sentimentScoreContainer"></div>
          </div>
        </div>

        <div class="col-lg-7 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow" >
          <div class="lead">
            <i style="color: rgb(92, 92, 92)" class="fas fa-cloud fa-lg"></i>
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              cloud 
            </span>
            <div class="float-left ml-1" >Word Cloud:</div> 
          </div>
          <!-- Create a div where the wordcloud will be -->
          <div class="container float-left" id="wordCloudContainer"></div>
          <!-- White background to the empty space of div when spinner is loading -->
          <div class="wordCloudWhiteBackground"></div>
          <!-- Spinner -->
          <div class="spinner-border text-secondary float-left spinner" id="wordCloudContainerSpinner" role="status" ></div>
        </div>
      </div>

      <div class="row" id="wordCloudReviewsContainer">
        <div class="col border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
            <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
              rate_review
            </span>
            <span style="font-size:33px; color: rgb(92, 92, 92)" 
                  onclick="(function(){document.getElementById('wordCloudReviewsContainer').style.display = 'none'})()"
                  class="material-icons float-right mr-2 closeReviews">
            close
            </span>
            <div>Selected Reviews:</div> 
          
            <hr>
            <!-- <div class="col-12 border"></div> -->
            <div id="wordCloudClickedReviews" class="scrollReviews">
            <!-- Reviews gets Populated Here -->
            </div>
            <hr>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
            <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
              timeline
            </span>
            <div class="float-left ml-1" >Sentiment Over Time:</div> 
            <br>
            <hr>
            <div class="ml-3"> 
              Select year: <select id="year"></select>

              <input type="checkbox" id="sort" class="ml-1">	Sort
            </div>
            <!-- <hr> -->
            <!-- <div class="col-12 border"></div> -->
            <div id="sentimentOverTimeContainerDiv">
              <svg class="container" id="sentimentOverTimeContainer" width="1000" height="400"></svg>
            </div>
            
            
          </div>
        </div>
      </div>

      <div class="row" id="sentimentReviewsContainer">
        <div class="col border border-secondary p-2 rounded mb-2 white-bg shadow">
          <div class="lead">
            <!-- <i style="color: rgb(92, 92, 92)"  class="fas fa-hourglass-half fa-lg"></i> -->
            <span style="font-size:33px; color: rgb(92, 92, 92)" class="material-icons float-left">
              rate_review
            </span>
            <span style="font-size:33px; color: rgb(92, 92, 92)" 
                  onclick="(function(){document.getElementById('sentimentReviewsContainer').style.display = 'none'})()"
                  class="material-icons float-right mr-2 closeReviews">
            close
            </span>
            <div>Selected Reviews:</div> 
            <hr>
            <!-- <div class="col-12 border"></div> -->
            <div id="sentimentOverTimeClickedReviews" class="scrollReviews">
            <!-- Reviews gets Populated Here -->
            </div>
            <hr>
          </div>
        </div>
      </div>

    </div>
  </main>
  <script>
  
   function test(){
     console.log("hi")
    }

    document.getElementById("wordCloudReviewsContainer").style.display = "none";
    document.getElementById("sentimentReviewsContainer").style.display = "none";
    document.getElementById("wordCloudContainer").style.display        = "block";
    document.getElementById("wordCloudContainerSpinner").style.display = "none";
    
    let storeIDByUser = document.getElementById('getStoreID').value;
    let shopID = (storeIDByUser == null) ? '1' : storeIDByUser;
    
    function makeRequest(url,method,values) {
      return new Promise(function (resolve, reject) {

        let request = new XMLHttpRequest();

        request.open(method, url);
        request.timeout = 20000;
        request.onload = function () {
            if (this.status >= 200 && this.status < 300) {
                resolve(request.response);
            } else {
                reject({
                    status: this.status,
                    statusText: request.statusText
                });
            }
        };
        request.onerror = function () {
            reject({
                status: this.status,
                statusText: request.statusText
            });
        };
        // request.setRequestHeader('Authorization', 'Bearer ' + token)
        request.setRequestHeader("Content-type", "application/JSON");
        request.setRequestHeader( 'Access-Control-Allow-Origin', '*');
        // request.withCredentials = false;
        request.send(values);
      });
    }

    let sentimentDataForWordCloud = [];
    async function getSentimentScore(){
      document.getElementById("myNav").style.display = "block";
      let adjNounPairs = await makeRequest("http://35.175.55.18:5000/reviews/" + shopID, "GET", "");
      let response     = JSON.parse(adjNounPairs).data;

      dataPrepForAllOtherThanWordCloud(response);
      sentimentOverTimePrepareData(response);
      document.getElementById("myNav").style.display = "none";
    }

    function dataPrepForAllOtherThanWordCloud(response){
      // Total Reviews Number
      let totalReviews = response.length;
      document.getElementById("totalNoOfReviewsContainer").textContent = totalReviews;

      // Sentiment Donut pos,neg,neu
      let pos          = [];
      let neg          = [];
      let neu          = [];

      //Overall Sentiment Score Accumulator
      let totalCompoundScores = 0;

      for(x in response){
        totalCompoundScores += response[x].compound_score;

        if(response[x].compound_score >= 0.05){
          pos.push({review_id   : response[x].review_id,
                    review_text : response[x].review_text});
        }
        else if(response[x].compound_score <= -0.05){
          neg.push({review_id   : response[x].review_id,
                    review_text : response[x].review_text});
        }
        else{
          neu.push({review_id   : response[x].review_id,
                    review_text : response[x].review_text});
        }
      }
      sentimentDataForWordCloud.push({Positive :pos,
                                      Negative :neg,
                                      Neutral  :neu});

      // console.log(sentimentDataForWordCloud);


      // dataToBeSentToServer[0].data.push(...sentimentDataForWordCloud[0]['Negative'], 
      //                                   ...sentimentDataForWordCloud[0]['Neutral'],
      //                                   ...sentimentDataForWordCloud[0]['Positive'])


      //Overall Sentiment Score 
      let overallSentimentScore = totalCompoundScores/totalReviews;
      displayStars(overallSentimentScore)
            
      //Sentiment Score
      prepareSentimentDonut(totalReviews);


      //Preparing data for adj clicks to see review
      refactorResponseForReviewsViaWordCloudAdjs(response);
    }  

    let refactoredResponse = {};
    function refactorResponseForReviewsViaWordCloudAdjs(response){
      // console.log(response);
      for (x in response){
        refactoredResponse[response[x].review_id] = {
                            compound_score : response[x].compound_score,
                            review_text    : response[x].review_text,
                            review_date    : response[x].review_date 
                          }
                              
      }
      // console.log(refactoredResponse)
    }

    //Temporary till we have user Login Feature
    async function getStoreName(){
      let storeInfo = await makeRequest("http://35.175.55.18:5000/stores/" + shopID, "GET", "");
      document.getElementById('shopNameNavBar').innerText = JSON.parse(storeInfo).data[0].store_name;
    }

    getStoreName();
    getSentimentScore();
  </script>

  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    
  </body>
</html>