// const form = document.getElementById("todo-form");
// const input = document.getElementById("todo-input");
// const todoLane = document.getElementById("todo-lane");

// // Function to fetch tasks from Symfony backend
// function fetchTasks() {
//   fetch('/tasks')
//     .then(response => response.json())
//     .then(tasks => {
//       tasks.forEach(task => {
//         renderTask(task);
//       });
//     })
//     .catch(error => {
//       console.error('Error fetching tasks:', error);
//     });
// }

// // Function to render a single task onto the webpage
// function renderTask(task) {
//   const newTaskElement = document.createElement("p");
//   newTaskElement.classList.add("task");
//   newTaskElement.setAttribute("draggable", "true");
//   newTaskElement.innerText = task.title; // Assuming 'title' is the property that represents the task title

//   // Add task to the appropriate lane based on its status
//   if (task.status === "todo") {
//     todoLane.appendChild(newTaskElement);
//   } else if (task.status === "doing") {
//     // Add logic for other lanes if needed
//   }
// }

// // Event listener for form submission
// form.addEventListener("submit", (e) => {
//   e.preventDefault();
//   const value = input.value;

//   if (!value) return;

//   const newTask = document.createElement("p");
//   newTask.classList.add("task");
//   newTask.setAttribute("draggable", "true");
//   newTask.innerText = value;

//   newTask.addEventListener("dragstart", () => {
//     newTask.classList.add("is-dragging");
//   });

//   newTask.addEventListener("dragend", () => {
//     newTask.classList.remove("is-dragging");
//   });

//   todoLane.appendChild(newTask);

//   input.value = "";
// });

// // Fetch tasks when the page loads
// document.addEventListener('DOMContentLoaded', () => {
//   fetchTasks();
// });