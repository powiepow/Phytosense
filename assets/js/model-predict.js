/*

* Phytosense main module includes realtime scanning, which involves the use of certain algorithm / Tensorflow.js

* Collects 1 frame

* Analyzes frame -> Returns prediction



*/


let model_used = "";
let modelFolderPath = ""; 
let metadataPath = "";

if (model_used) {
  console.log(model_used);
}

let model,
  classLabels = [],
  stopCamera = false,
  predictionTimer = null,
  predictionTimeLimit = 3000, 
  previousPrediction = "",
  disease_name = "",
  percentage = "";


const camera = document.getElementById("camera");
const labelContainer = document.getElementById("label-container");
const responseMessage = document.getElementById("responseMessage");
const diseaseName = document.getElementById("disease-name");
const loaderWrapper = document.getElementById("loader-wrapper");
const startButton = document.getElementById("startButton");
const stopButton = document.getElementById("stopButton");

// INITIALIZE model and metadata
async function init() {
  try {
    const metadataResponse = await fetch(metadataPath);
    const metadata = await metadataResponse.json();
    classLabels = metadata.labels || [];
    responseMessage.textContent = "Model metadata loaded successfully.";
    //console.log("Labels:", classLabels);
  } catch (error) {
    console.error("Error loading metadata:", error);
    responseMessage.textContent = "Failed to load model metadata.";
  }
}

// START the webcam and prediction loop
async function initWebcam() {
  try {
    loaderWrapper.classList.remove("hide"); // Show loader
    stopCamera = false;

    startButton.style.cursor = "not-allowed";
    startButton.disabled = true;

    // LOAD the model
    model = await tf.loadLayersModel(modelFolderPath);
    console.log("Model loaded successfully:", model);

    // START the webcam
    const stream = await navigator.mediaDevices.getUserMedia({
      //video: { width: 320, height: 300 },
      video: { facingMode: "environment", width: 320, height: 300 },
    });
    camera.srcObject = stream;
    await camera.play();
    responseMessage.textContent = "Webcam started successfully.";
    loaderWrapper.classList.add("hide"); 

    //START prediction functions
    startPredictions(); 
  } catch (error) {
    console.error("Error starting webcam:", error);
    responseMessage.textContent = "Failed to start webcam.";
    loaderWrapper.classList.add("hide"); // Hide loader
  }
}

// PREDICTION loop
async function startPredictions() {
  if (stopCamera) return;

  await predict();
  setTimeout(startPredictions, 1000); 
}

// PREDICT the class from the webcam feed
async function predict() {
  try {
    if (!camera.srcObject || camera.videoWidth === 0 || camera.videoHeight === 0) {
      console.warn("Webcam not ready for prediction.");
      return;
    }

    // PREPROCESS the video feed for prediction
    const predictionTensor = tf.tidy(() => {
      return tf.browser
        .fromPixels(camera)
        .resizeNearestNeighbor([224, 224]) 
        .toFloat()
        .div(127.5)
        .sub(1) // NORMALIZE to range [-1, 1]
        .expandDims();
    });

    // PERFORM the prediction
    const predictions = await model.predict(predictionTensor).data();
    predictionTensor.dispose(); // Dispose tensor to prevent memory leaks

    // FIND the class with the highest probability
    const maxConfidence = Math.max(...predictions);
    const maxIndex = predictions.indexOf(maxConfidence);

    // ASSIGN the label and confidence
    disease_name = classLabels[maxIndex] || "Unknown";
    percentage = (maxConfidence * 100).toFixed(2);

    // DISPLAY the prediction
    if (maxConfidence < 0.5) {
      labelContainer.innerHTML = `<span style='color:orange;'>Prediction unclear</span>`;
      resetTimer();
    } else {
      if(disease_name === "Others"){
        labelContainer.innerHTML = `<i style="color:red;">No crop detected</i>`;
      }else{
        labelContainer.innerHTML = `Prediction: <b>${disease_name}</b> ${percentage}%`;
      }
      //labelContainer.innerHTML = `Prediction: <b>${disease_name}</b> ${percentage}%`;

      if (disease_name === "Others" || disease_name === "Healthy") {
        resetTimer();
        diseaseName.href = "#";
      } else {
        if (disease_name === previousPrediction) {
          if (!predictionTimer) {
            predictionTimer = setTimeout(() => {
              loaderWrapper.classList.remove("hide"); 
              callAjax(); 
            }, predictionTimeLimit);
          }
        } else {
          resetTimer();
          previousPrediction = disease_name; 
        }
      }

      $("#disease-name")
        .off("click")
        .on("click", function () {
          resetTimer(); // Stop automatic AJAX call
          callAjax();
        });
    }
  } catch (error) {
    console.error("Error during prediction:", error);
    responseMessage.textContent = "Error making a prediction.";
  }
}


function resetTimer() {
  if (predictionTimer) {
    clearTimeout(predictionTimer);
    predictionTimer = null;
  }
  previousPrediction = "";
}

// AJAX call to send prediction to the server
function callAjax() {
  $.ajax({
    method: "POST",
    url: "/crud",
    data: {
      disease_prediction: disease_name,
      prediction_percentage: percentage,
    },
    success: function (response) {
      loaderWrapper.classList.add("hide"); 
      if (response === "!found") {
        swal({
          icon: "error",
          title: "Error",
          text: "Disease not recorded",
        });
      } else {
        console.log(response);
        $("#responseMessage").html(response);
      }
    },
    error: function (xhr, status, error) {
      loaderWrapper.classList.add("hide"); 
      $("#responseMessage").html("An error occurred: " + error);
      swal({
        icon: "error",
        title: "Error",
        text: "An error occurred: " + error,
      });
    },
  });
}

// STOP the webcam
function stopWebcam() {
  stopCamera = true;
  if (camera.srcObject) {
    const stream = camera.srcObject;
    const tracks = stream.getTracks();
    tracks.forEach((track) => track.stop());
    camera.srcObject = null;
    responseMessage.textContent = "Webcam stopped.";

    startButton.style.cursor = "pointer";
    startButton.disabled = false;
  }
}

// MODEL used
function fetchModelUsed() {
  try {
    $.ajax({
      url: "/model_used", 
      method: "GET",
      dataType: "text",
      success: function (data) {
        model_used = data; 
        modelFolderPath = `../../machine-learning/${model_used}/model.json`;
        metadataPath = `../../machine-learning/${model_used}/metadata.json`;

        //console.log("Model folder path:", modelFolderPath);
        //console.log("Metadata path:", metadataPath);

        init(); 
      },
      error: function (xhr, status, error) {
        console.error(`Error during AJAX call: ${status}, ${error}`);
      },
    });
  } catch (error) {
    console.error("Error during AJAX setup:", error);
  }
}

// INITIALIZE the app on page load
window.onload = fetchModelUsed;

// EVENT Listeners
startButton.addEventListener("click", () => initWebcam(modelFolderPath));
stopButton.addEventListener("click", stopWebcam);





