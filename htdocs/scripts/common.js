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
      request.setRequestHeader('Access-Control-Allow-Origin', '*');
    //   request.withCredentials = false;
      request.send(values);
    });
}

function makeRequestxwwwFormURLEncode(url,method,values) {
    return new Promise(function (resolve, reject) {

    let request = new XMLHttpRequest();

    request.open(method, url,true);
    request.timeout = 5000;
    request.onload = function () {
        if (this.status >= 200 && this.status <= 500) {
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

    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(values);

    });
}

var hostname               = "http://35.175.55.18:5000";
var datableRowsPerPage     = 25; //10,25,50 or 100 only
var emailSubjectToCustomer = `FlourishingOurLocale_Dashboard_Status_Update`;
var emailBodyToCustomer    = 
`
We have viewed and verified your credentials.
We are glad to inform you that your account has been activated.

Lin Yanling
Senior Place Manager
Urban Design, Central
Urban Redevelopment Authority Team
`