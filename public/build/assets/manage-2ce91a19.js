document.querySelectorAll('[data-target="#editModal"]').forEach(e=>{e.addEventListener("click",async l=>{var a=l.target,t=a.getAttribute("data-loadout-id");try{const n=await(await fetch("/loadouts/"+t,{method:"GET",headers:{"X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content")}})).json();document.querySelector("#editModal #title").value=n.title;const d=n.parameters.map(r=>r.id);document.querySelectorAll('#editModal input[name="parameters[]"]').forEach(r=>{r.checked=d.includes(parseInt(r.value))})}catch(o){console.error("There was an error:",o)}})});document.querySelectorAll(".add-parameter-button").forEach(e=>{e.addEventListener("click",()=>{document.querySelector("#mainModalLabel").textContent="Ajouter un paramètre",document.querySelector("#mainModalBody").innerHTML=`
            <form method="POST" action="/parameters" class="w-full max-w-lg">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute("content")}">
                <div class="flex flex-wrap mb-6 -mx-3">
                    <div class="w-full px-3">
                        <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="label">
                            Label
                        </label>
                        <input id="label" type="text" name="label" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border rounded appearance-none focus:outline-none focus:bg-white" required>
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                    Créer
                </button>
            </form>
        `;const l=document.querySelector("#mainModalBody #label"),a=document.querySelector('#mainModalBody button[type="submit"]');l.addEventListener("input",()=>{l.value.length>15?(a.disabled=!0,a.title="Le label ne peut pas contenir plus de 15 caractères."):(a.disabled=!1,a.title="")}),$("#mainModal").modal("show")})});document.querySelectorAll(".add-loadout-button").forEach(e=>{e.addEventListener("click",async()=>{document.querySelector("#mainModalLabel").textContent="Ajouter un modèle";const t=(await(await fetch("/parameters")).json()).map(o=>`
            <div class="flex items-center mt-2">
                <input id="parameter${o.id}" type="checkbox" name="parameters[]" value="${o.id}" class="w-5 h-5 text-blue-600 form-checkbox">
                <label for="parameter${o.id}" class="ml-2 text-sm text-gray-600">${o.label}</label>
            </div>
        `).join("");document.querySelector("#mainModalBody").innerHTML=`
            <form method="POST" action="/loadouts" class="w-full max-w-lg">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute("content")}">
                <div class="mb-6 -mx-3">
                    <div class="items-center mt-2">
                        <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="title">
                            Titre
                        </label>
                        <input id="title" type="text" name="title" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border rounded appearance-none focus:outline-none focus:bg-white" required>
                    </div>
                </div>
                <div class="flex flex-wrap mb-6 -mx-3">
                    <div class="w-full px-3">
                        <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase">
                            Paramètres
                        </label>
                        ${t}
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                    Créer
                </button>
            </form>
        `,$("#mainModal").modal("show"),s()})});document.querySelectorAll(".edit-button").forEach(e=>{e.addEventListener("click",async()=>{var l=e.getAttribute("data-loadout-id");const t=await(await fetch("/loadouts/"+l)).json();document.querySelector("#mainModalLabel").textContent="Modifier le modèle";const d=(await(await fetch("/parameters")).json()).map(r=>`
            <div class="flex items-center mt-2">
                <input id="parameter${r.id}" type="checkbox" name="parameters[]" value="${r.id}" ${t.parameters.find(i=>i.id===r.id)?"checked":""} class="w-5 h-5 text-blue-600 form-checkbox">
                <label for="parameter${r.id}" class="ml-2 text-sm text-gray-600">${r.label}</label>
            </div>
        `).join("");document.querySelector("#mainModalBody").innerHTML=`
            <form method="POST" action="/loadouts/${t.id}" class="w-full max-w-lg">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute("content")}">
                <input type="hidden" name="_method" value="PUT">
                <div class="mb-6 -mx-3">
                    <div class="w-full px-3">
                        <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="title">
                            Titre
                        </label>
                        <input id="title" type="text" name="title" value="${t.title}" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border rounded appearance-none focus:outline-none focus:bg-white">
                    </div>
                </div>
                <div class="mb-6 -mx-3">
                    <div class="w-full px-3">
                        <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase">
                            Paramètres
                        </label>
                        ${d}
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                    Modifier
                </button>
            </form>
        `,$("#mainModal").modal("show"),s()})});function s(){const e=document.querySelector('#mainModalBody button[type="submit"]'),l=document.querySelectorAll('#mainModalBody input[name="parameters[]"]:checked').length;l<1||l>8?(e.disabled=!0,e.title="Vous devez sélectionner au moins un paramètre et au maximum 8."):(e.disabled=!1,e.title=""),document.querySelectorAll('#mainModalBody input[name="parameters[]"]').forEach(a=>{a.addEventListener("change",()=>{const t=document.querySelectorAll('#mainModalBody input[name="parameters[]"]:checked').length;t<1||t>8?(e.disabled=!0,e.title="Vous devez sélectionner au moins un paramètre et au maximum 8."):(e.disabled=!1,e.title="")})})}document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll("#delete-param").forEach(a=>{a.addEventListener("click",function(t){t.preventDefault();const o=t.target.closest("form");window.confirmationAlert("Voulez-vous vraiment supprimer ce paramètre ?").then(n=>{n&&o.submit()})})}),document.querySelectorAll("#delete-load").forEach(a=>{a.addEventListener("click",function(t){t.preventDefault(),window.confirmationAlert("Voulez-vous vraiment supprimer ce modèle ?").then(o=>{o&&t.target.closest("form").submit()})})})});
