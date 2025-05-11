
const uploadPhotoInput = document.getElementById("uploadPhoto");
const labelContainer = document.getElementById("label-container");
const loaderWrapper = document.getElementById("loader-wrapper");

let model, classLabels = [];
let modelFolderPath = ""; 
let metadataPath = ""; 
const confidenceThreshold = 0.6; 


function fetchModelForUpload() {
  $.ajax({
    url: "/model_used",
    method: "GET",
    dataType: "text",
    success: function (data) {
      const model_used = data;

    
      modelFolderPath = `../../machine-learning/${model_used}/model.json`;
      metadataPath = `../../machine-learning/${model_used}/metadata.json`;



      initUploadModel();
    },
    error: function (xhr, status, error) {
      console.error(`Error during AJAX call: ${status}, ${error}`);
    },
  });
}


async function initUploadModel() {
  try {
    model = await tf.loadLayersModel(modelFolderPath);

    const metadataResponse = await fetch(metadataPath);
    const metadata = await metadataResponse.json();

    classLabels = metadata.labels || [];
  } catch (error) {
    console.error("Error initializing upload model:", error);
  }
}

uploadPhotoInput.addEventListener("change", handlePhotoUpload);

async function handlePhotoUpload(event) {
  const file = event.target.files[0];
  if (!file || !file.type.startsWith("image/") || file.size > 5 * 1024 * 1024) {
    swal({
      icon: "warning",
      title: "Invalid file",
      text: "Please upload a valid image file (max 5MB).",
    });
    return;
  }

  try {
    loaderWrapper.classList.remove("hide"); 
    const imageTensor = await readImage(file);
    await predictUpload(imageTensor);
    loaderWrapper.classList.add("hide"); 
  } catch (error) {
    console.error("Error during upload prediction:", error);
    loaderWrapper.classList.add("hide"); 
    labelContainer.innerHTML = `<span style="color:red;">Error predicting the image</span>`;
  }
}

async function readImage(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = new Image();
      img.src = e.target.result;
      img.onload = () => {
        const canvas = document.createElement("canvas");
        canvas.width = 224;
        canvas.height = 224;
        const ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        const imageTensor = tf.browser
          .fromPixels(canvas)
          .resizeNearestNeighbor([224, 224]) 
          .toFloat()
          .div(127.5)
          .sub(1) 
          .expandDims(0); 
        resolve(imageTensor);
      };
    };
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

async function predictUpload(imageTensor) {
  try {
    const predictions = await model.predict(imageTensor).data();
    imageTensor.dispose(); 

    const maxConfidence = Math.max(...predictions);
    const maxIndex = predictions.indexOf(maxConfidence);

    const disease_name = classLabels[maxIndex] || `Class ${maxIndex}`;
    const percentage = (maxConfidence * 100).toFixed(2);

    if (maxConfidence < confidenceThreshold) {
      swal({
        icon: "info",
        title: "Result",
        text: `Prediction: ${disease_name} (${percentage}%)`,
      });
      return;
    }else if(disease_name == "Healthy"){
      swal({
        icon: "info",
        title: "Result",
        text: `Prediction: ${disease_name} (${percentage}%)`,
      });
      return;
    }else if(disease_name == "Others"){
      swal({
        icon: "info",
        title: "No Crops Detected",
      });
      return;
    }

    sendUploadPrediction(disease_name, percentage);
  } catch (error) {
    console.error("Error during upload prediction:", error);
    labelContainer.innerHTML = `<span style="color:red;">Prediction failed</span>`;
  }
}

function sendUploadPrediction(disease_name, percentage) {
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
        console.log("Server response:", response);
        $("#responseMessage").html(response);
      }
    },
    error: function (xhr, status, error) {
      loaderWrapper.classList.add("hide");
      console.error("Error sending upload prediction:", error);
      swal({
        icon: "error",
        title: "Error",
        text: `Error sending prediction: ${error}`,
      });
    },
  });
}

fetchModelForUpload();
