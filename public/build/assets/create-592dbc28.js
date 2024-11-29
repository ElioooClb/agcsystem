const l=document.querySelector(".btn-assign"),d=document.querySelector(".ulListeTeck"),i=document.getElementById("assignedUsersInput");l.addEventListener("click",function(){document.querySelectorAll(".selected-users option:checked").forEach(e=>{const t=document.createElement("li");t.className="d-flex assignedUser",t.innerHTML=`
            <p>${e.textContent}</p>
            <button class="deleteAssignedUser" data-id-user="${e.value}" data-name-user="${e.textContent}" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-trash">
                    <path
                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                    <path
                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                </svg>
            </button>
        `,d.appendChild(t);const n=t.querySelector(".deleteAssignedUser");n.addEventListener("click",function(u){const c=n.getAttribute("data-id-user"),o=n.getAttribute("data-name-user"),s=document.createElement("option");s.className="text-2xl",s.value=c,s.text=o,document.querySelector(".selected-users").add(s),t.remove(),r()}),e.remove(),r()})});function r(){const a=Array.from(d.querySelectorAll(".assignedUser")).map(e=>e.querySelector(".deleteAssignedUser").getAttribute("data-id-user"));i.value=JSON.stringify(a)}
