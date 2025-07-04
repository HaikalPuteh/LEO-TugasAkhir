import './bootstrap';
console.log("Hello from app.js!");

document.addEventListener('DOMContentLoaded', () => {
    const messageDiv = document.createElement('div');
    messageDiv.textContent = 'JavaScript loaded successfully!';
    document.body.appendChild(messageDiv);
});