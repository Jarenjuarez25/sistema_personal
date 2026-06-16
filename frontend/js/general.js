    
//Toggle  visibility
    
    const toggleBtn = document.getElementById('togglePwd');
    const pwdInput  = document.getElementById('password');
 
    toggleBtn.addEventListener('click', () => {
        const isHidden = pwdInput.type === 'password';
        pwdInput.type = isHidden ? 'text' : 'password';
        toggleBtn.querySelector('i').className = isHidden ? 'fa fa-eye' : 'fa fa-eye-slash';
    });