const obeserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if(entry.isIntersecting){
            entry.target.classList.add('show');
        }else{
            entry.target.classList.remove('show');
        }
    });
});

const hidden2 = document.querySelectorAll('.hidden-2');
const hiddenElements = document.querySelectorAll('.hidden');
hiddenElements.forEach((el) => obeserver.observe(el));
hidden2.forEach((el) => obeserver.observe(el));


  
document.addEventListener('click', (event) => {
  const link = event.target.closest('[data-target]'); 

  if (link) {
    document.querySelectorAll('[data-target]').forEach(el => el.classList.remove('active__link'));
    document.querySelectorAll('[content]').forEach(el => el.classList.remove('active__link'));

    
    link.classList.add('active__link');
    const targetSection = document.querySelector(link.dataset.target);
    targetSection.classList.add('active__link'); 
  }
});


function settings() {
  const box = document.getElementById("post__settings");
  box.classList.toggle("open");
  
}

function image_upload_preview() {
  const image_prev = document.getElementById("upload-preview");
  const upload_tag = document.getElementById("upload-image");
  const upload_profile = document.getElementById("upload-profile");
  let selectedImages = []; 

  if (upload_tag) {
      upload_tag.addEventListener('change', function() {
          const files = this.files;

          if (files.length > 3) {
              swal({
                icon: "warning",
                title:"Maximum Upload",
                text: "We're sorry, we only accept up to 3 images.",
              })
              this.value = ''; 
              image_prev.innerHTML = '<i class="fa-solid fa-upload"></i>'; 
              return;
          }else{
            image_prev.innerHTML = '<i class="fa-solid fa-upload"></i>';
          }

          selectedImages = Array.from(files); 
          image_prev.innerHTML = ''; 

          selectedImages.forEach((file) => {
              const reader = new FileReader();
              reader.addEventListener('load', function() {
                  const imgElement = document.createElement('img');
                  imgElement.src = this.result;
                  imgElement.alt = 'Image Preview';
                  imgElement.style.width = '150px';
                  imgElement.style.margin = '5px';
                  image_prev.appendChild(imgElement); 
              });
              reader.readAsDataURL(file); 
              
          });
      });
  } else {
      upload_profile.addEventListener('change', function() {
          const profile = this.files[0];
          selectedImage = profile;
          if (profile) {
              const read = new FileReader();
              read.addEventListener('load', function() {
                  image_prev.innerHTML = `<img class="pf-image" src="${this.result}" alt="Image Preview">`;
              });
              read.readAsDataURL(profile);
          }
      });
  }
  
}


function max_character(){
const charCount = document.getElementById('char-count');
const desc = document.getElementById('desc-area');
let max_char = 250;

desc.addEventListener('input', function(){
  if(this.value.length > max_char){
    this.value = this.value.substring(0, max_char);
  }
  charCount.innerHTML=this.value.length;
});


}
