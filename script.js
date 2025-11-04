(function(){
      const form = document.getElementById('regForm');
      const submitBtn = document.getElementById('submitBtn');
      const resetBtn = document.getElementById('resetBtn');
      const fileInput = document.getElementById('receipt');
      const fileName = document.getElementById('fileName');
      const summary = document.getElementById('summaryList');
      const successMsg = document.getElementById('successMsg');
      const progressBar = document.getElementById('progressBar');

      function setError(name, msg){
        const el = document.querySelector('[data-error-for="'+name+'"]');
        if(el) el.textContent = msg || '';
      }

      function clearErrors(){
        document.querySelectorAll('[data-error-for]').forEach(e=>e.textContent='');
      }

      fileInput.addEventListener('change', ()=>{
        fileName.textContent = fileInput.files.length ? fileInput.files[0].name : 'Brak pliku';
      });

      function validate(data){
        const errors = {};
        if(!data.firstName) errors.firstName = 'Imię jest wymagane';
        if(!data.lastName) errors.lastName = 'Nazwisko jest wymagane';
        if(!data.email) errors.email = 'E‑mail jest wymagany';
        else if(!/^\S+@\S+\.\S+$/.test(data.email)) errors.email = 'Nieprawidłowy e‑mail';
        if(!data.productModel) errors.productModel = 'Model produktu jest wymagany';
        if(!data.serialNumber) errors.serialNumber = 'Numer seryjny jest wymagany';
        if(!data.purchaseDate) errors.purchaseDate = 'Data zakupu jest wymagana';
        if(!data.agree) errors.agree = 'Musisz zaakceptować warunki';
        return errors;
      }

      function gather(){
        return {
          firstName: form.firstName.value.trim(),
          lastName: form.lastName.value.trim(),
          email: form.email.value.trim(),
          phone: form.phone.value.trim(),
          productModel: form.productModel.value.trim(),
          serialNumber: form.serialNumber.value.trim(),
          purchaseDate: form.purchaseDate.value,
          store: form.store.value.trim(),
          receipt: fileInput.files[0] || null,
          agree: form.agree.checked
        };
      }

      function renderSummary(data){
        summary.innerHTML = `
          <div><strong>Użytkownik:</strong> ${escapeHtml(data.firstName)} ${escapeHtml(data.lastName)}</div>
          <div><strong>E‑mail:</strong> ${escapeHtml(data.email)}</div>
          <div><strong>Model / SN:</strong> ${escapeHtml(data.productModel)} / ${escapeHtml(data.serialNumber)}</div>
          <div><strong>Data zakupu:</strong> ${escapeHtml(data.purchaseDate)}</div>
        `;
      }

      function escapeHtml(s){ if(!s) return ''; return s.replace(/[&<>\"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[c]); }

      submitBtn.addEventListener('click', ()=>{
        clearErrors();
        successMsg.style.display = 'none';
        const data = gather();
        const errors = validate(data);
        if(Object.keys(errors).length){
          Object.entries(errors).forEach(([k,v])=>setError(k,v));
          return;
        }

        // Pokaż podsumowanie
        renderSummary(data);

        // Symuluj upload z progressbarem
        let pct = 0;
        progressBar.style.width = '0%';
        const t = setInterval(()=>{
          pct += Math.random()*18 + 6;
          if(pct>=100){ pct = 100; progressBar.style.width = '100%'; clearInterval(t); successMsg.style.display='block'; }
          else progressBar.style.width = pct + '%';
        }, 300);

        // W prawdziwej aplikacji tutaj wyślij FormData przez fetch
      });

      resetBtn.addEventListener('click', ()=>{
        form.reset(); fileName.textContent='Brak pliku'; clearErrors(); summary.innerHTML='Wypełnij formularz aby zobaczyć podsumowanie.'; successMsg.style.display='none'; progressBar.style.width='0%';
      });

      // Podstawowa walidacja live
      ['firstName','lastName','email','productModel','serialNumber','purchaseDate','agree'].forEach(name=>{
        const el = form[name];
        if(!el) return;
        el.addEventListener('input', ()=> setError(name, ''));
        el.addEventListener('change', ()=> setError(name, ''));
      });

    })();