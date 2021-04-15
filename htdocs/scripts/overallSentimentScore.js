function displayStars(overallSentimentScore, isForURARankedContainingStoreID){

    if(isForURARankedContainingStoreID){
      starsType    = "rankedStars"
      placeStarsAt = "rankOverallSentimentOverTime"+isForURARankedContainingStoreID
    }else{
      starsType    = "normalStars"
      placeStarsAt = "overallSentimentScore"
    }
    
    //CompountScore  Stars
      //-1             0
      //-0.99 to -0.8  0.5
      //-0.79 to -0.6  1
      //-0.59 to -0.4  1.5
      //-0.39 to -0.2  2
      //-0.19  to 0.19  2.5
      //0.2   to 0.39  3
      //0.4   to 0.59  3.5
      //0.6   to 0.79  4
      //0.8   to 0.99  4.5
      //1              5 
      // let overallSentimentScore = -1
    //   console.log(overallSentimentScore);
    if(overallSentimentScore == 1){
      // console.log("5 Stars");
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>`;
    }
    else if(overallSentimentScore >= 0.8){
      // console.log("4.5 Stars");
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star_half
      </span>`;
    }
    else if(overallSentimentScore >= 0.6){
      // console.log("4 Stars");  
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>`;
    }
    else if(overallSentimentScore >= 0.4){
      // console.log("3.5 Stars"); 
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star_half
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>`;
    }
    else if(overallSentimentScore >= 0.2){
      // console.log("3 Stars"); 
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>`;
    }
    else if(overallSentimentScore >= -0.199999){
      // console.log("2.5 Stars"); 
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star_half
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>`;
    }
    else if(overallSentimentScore >= -0.399999){
      // console.log("2 Stars"); 
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>`;
    }
    else if(overallSentimentScore >= -0.59999){
      // console.log("1.5 Stars"); 
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star_half
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>`;
    }
    else if(overallSentimentScore >= -0.79999 ){
      // console.log("1 Stars"); 
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>`;
    }
    else if(overallSentimentScore >= -0.99999 ){
      // console.log("0.5 Stars"); 
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star_half
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>`;
    }
    else if(overallSentimentScore == -1){
      // console.log("0 Stars");
      document.getElementById(placeStarsAt).innerHTML =
      `<span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>
      <span class="material-icons ${starsType}">
      star_border
      </span>`;
    }
}