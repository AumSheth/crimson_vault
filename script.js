function togglePassword(id, iconElement) {
  const input = document.getElementById(id);
  if (input.type === 'password') {
    input.type = 'text';
    iconElement.textContent = '👁';
  } else {
    input.type = 'password';
    iconElement.textContent = '👁‍🗨'; // strikethrough style
  }
}
// Toggle edit form display for each case
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const caseId = btn.getAttribute('data-caseid');
        const formDiv = document.getElementById('edit-form-' + caseId);
        formDiv.style.display = formDiv.style.display === 'none' ? 'block' : 'none';
    });
});
