class User {
  constructor(id, email, drewno, kamien, zboze, dnipremium, image_url) {
    this.id = id;
    this.email = email;
    this.drewno = drewno;
    this.kamien = kamien;
    this.zboze = zboze;
    this.dnipremium = dnipremium;
    this.image_url = image_url;
  }
}

let usersList = [];
for (let i = 0; i < usersData.length; i++) {
  const user = usersData[i];
  const userObject = new User(
    user.id,
    user.email,
    user.drewno,
    user.kamien,
    user.zboze,
    user.dnipremium,
    user.image_url
  );
  usersList.push(userObject);
  console.log(userObject.email);
}
let listOfApprovedUsers = [];
const tableContent = document.getElementById("tableContent");
displayData(usersList);
function displayData(list) {
  tableContent.innerHTML = "";
  list.forEach((user) => {
    tableContent.innerHTML += `
    <tr>
        <td>${user.id}</td>
        <td>${user.email}</td>
        <td>${user.drewno}</td>
        <td>${user.kamien}</td>
        <td>${user.zboze}</td>
        <td>${user.dnipremium}</td>
        <td>${user.image_url}</td>
        <button id="${user.id}" onclick="addToApproved(this.id)">approve </button>
    </tr>
    `;
  });
}
function addToApproved(id) {
  const isApproved = listOfApprovedUsers.includes(id);
  if (isApproved) {
    const indexToDelete = listOfApprovedUsers.indexOf(id);
    listOfApprovedUsers.splice(indexToDelete, 1);
  } else {
    listOfApprovedUsers.push(id);
  }
  console.log(listOfApprovedUsers);
}

const form = document.getElementById("form");
form.addEventListener("submit", sendData);

function sendData() {
  const formData = new FormData();
  formData.append("listOfApprovedUsers", JSON.stringify(listOfApprovedUsers));
  fetch("saveApproved.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((data) => {
      console.log(data);
    })
    .catch((error) => {
      console.log("Error:", error);
    });
    console.log("wyslano");
}
