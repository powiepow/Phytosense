


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

async function init() {
  try {
    const metadataResponse = await fetch(metadataPath);
    const metadata = await metadataResponse.json();
    classLabels = metadata.labels || [];
    responseMessage.textContent = "Model metadata loaded successfully.";
    
  } catch (error) {
    console.error("Error loading metadata:", error);
    responseMessage.textContent = "Failed to load model metadata.";
  }
}

async function initWebcam() {
  try {
    loaderWrapper.classList.remove("hide"); 
    stopCamera = false;

    startButton.style.cursor = "not-allowed";
    startButton.disabled = true;

    model = await tf.loadLayersModel(modelFolderPath);
    console.log("Model loaded successfully:", model);

    const stream = await navigator.mediaDevices.getUserMedia({
     
      video: { facingMode: "environment", width: 320, height: 300 },
    });
    camera.srcObject = stream;
    await camera.play();
    responseMessage.textContent = "Webcam started successfully.";
    loaderWrapper.classList.add("hide"); 

    startPredictions(); 
  } catch (error) {
    console.error("Error starting webcam:", error);
    responseMessage.textContent = "Failed to start webcam.";
    loaderWrapper.classList.add("hide"); 
  }
}

async function startPredictions() {
  if (stopCamera) return;

  await predict();
  setTimeout(startPredictions, 1000); 
}

async function predict() {
  try {
    if (!camera.srcObject || camera.videoWidth === 0 || camera.videoHeight === 0) {
      console.warn("Webcam not ready for prediction.");
      return;
    }

    const predictionTensor = tf.tidy(() => {
      return tf.browser
        .fromPixels(camera)
        .resizeNearestNeighbor([224, 224]) 
        .toFloat()
        .div(127.5)
        .sub(1) 
        .expandDims();
    });

    const predictions = await model.predict(predictionTensor).data();
    predictionTensor.dispose(); 

    const maxConfidence = Math.max(...predictions);
    const maxIndex = predictions.indexOf(maxConfidence);

    disease_name = classLabels[maxIndex] || "Unknown";
    percentage = (maxConfidence * 100).toFixed(2);

    if (maxConfidence < 0.5) {
      labelContainer.innerHTML = `<span style='color:orange;'>Prediction unclear</span>`;
      resetTimer();
    } else {
      if(disease_name === "Others"){
        labelContainer.innerHTML = `<i style="color:red;">No crop detected</i>`;
      }else{
        labelContainer.innerHTML = `Prediction: <b>${disease_name}</b> ${percentage}%`;
      }

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
          resetTimer(); 
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

window.onload = fetchModelUsed;

startButton.addEventListener("click", () => initWebcam(modelFolderPath));
stopButton.addEventListener("click", stopWebcam);





