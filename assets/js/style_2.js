let sidebar = document.getElementById('mnu-btn');
$('#mnu-btn').click(function() {
    $('.sidebar').toggleClass('active');
});

$("#option").click(function(){
    $(".up_optn").toggleClass('clck');
});
 
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



  function image_upload_preview(){
    const image_prev = document.getElementById("pd_img_label");
    const upload_tag = document.getElementById("pd-image");
    let selectedImage;
    
    upload_tag.addEventListener('change', function() {
      const file = this.files[0];
      selectedImage = file;
      console.log(selectedImage);
      if (file) {
          const reader = new FileReader();
          reader.addEventListener('load', function() {
              image_prev.innerHTML = `<img src="${this.result}" alt="Image Preview">`;
          });
          reader.readAsDataURL(file);
      } else {
          image_prev.innerHTML = '<i class="fa-solid fa-upload"></i>';
      }
     });
    
  
    
  }

