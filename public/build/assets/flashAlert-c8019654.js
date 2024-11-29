import{S as r}from"./sweetalert2.esm.all-509645c6.js";function s(t="info",e,o=3e3){r.mixin({toast:!0,position:"top-end",showConfirmButton:!1,timer:o,timerProgressBar:!0}).fire({icon:t,text:e,showClass:{popup:`
              animate__animated
              animate__fadeInRight
              animate__faster
            `},hideClass:{popup:`
              animate__animated
              animate__fadeOutRight
              animate__faster
            `}})}function u(t="Êtes-vous sûr de vouloir procéder?",e="",o="warning"){return new Promise(n=>{r.fire({title:t,text:e,icon:o,showCancelButton:!0,confirmButtonColor:"#3085d6",confirmButtonText:"Valider",cancelButtonColor:"#d33",cancelButtonText:"Annuler",reverseButtons:!0,buttonsStyling:!1,customClass:{cancelButton:"order-1 bg-gray-500 text-dark font-bold py-2 px-2 rounded opacity-50",confirmButton:"order-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg mx-3"}}).then(a=>{n(a.isConfirmed)})})}export{u as c,s as f};
