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

    <!-- Jie Lin can put ur script here -->
    <!-- Load sentimentScoreOverTime from scripts folder -->

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
        /* background-color:  */
        /* border:1px solid black */
      }

      #wordCloudContainer{
        border: 1px solid rgb(36, 36, 36);
        border-radius: 7px;
      }

      /* #sentimentScoreContainer{
        /* height:500px; */
      } */

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
    </style>
  </head>
  <body>
    
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
      <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">SG Taps</a>
      <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </header>

    <main class="container">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>

        <button class="btn btn-primary">Update</button>
      </div>

      <form>
        <input type="text" name=storeID>
        <input type="submit">
      </form>
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
          <div class="col-lg-5 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <div class="lead">
              <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
                tag_faces
              </span>
              <div class="float-left ml-1" >Sentiment Score:</div> 
              <!-- Create a div where the sentimentScore will be -->
              <div class="container float-left" id="sentimentScoreContainer"></div>
            </div>
          </div>
          <div class="col-lg-7 col-md-6 col-sm-12 border border-secondary p-2 rounded mb-2 white-bg shadow">
            <div class="lead">
              <!-- <i style="color: rgb(92, 92, 92)" class="fas fa-cloud fa-lg"></i> -->
              <span style="font-size:30px; color: rgb(92, 92, 92)" class="material-icons float-left">
                cloud 
              </span>
              <div class="float-left ml-1" >Word Cloud:</div> 
            </div>
            <!-- Create a div where the wordcloud will be -->
            <div class="container float-left" id="wordCloudContainer"></div>
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
              <!-- <div class="col-12 border"></div> -->
              <!-- Jie Lin can put ur Div here -->
            </div>
          </div>
        </div>
      </div>
    </main>

  <script>
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
      let storeIDByUser = document.getElementById('getStoreID').value;
      let shopID = (storeIDByUser == null) ? '1' : storeIDByUser;

      var adjNounPairs = await makeRequest("http://35.175.55.18:5000/reviews/" + shopID, "GET", "");
      let response     = JSON.parse(adjNounPairs).data;

      dataPrepForAllOtherThanWordCloud(response);
    }


    function dataPrepForAllOtherThanWordCloud(response){
      // Total Reviews Number
      let totalReviews = response.length;
      document.getElementById("totalNoOfReviewsContainer").textContent = totalReviews ;

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


      //Overall Sentiment Score 
      let overallSentimentScore = totalCompoundScores/totalReviews;
      displayStars(overallSentimentScore)
            
      //Sentiment Score
      prepareSentimentDonut(totalReviews);
    }  
  </script>

  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    
  </body>
</html>