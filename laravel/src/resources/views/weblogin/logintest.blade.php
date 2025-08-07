<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuta Social Club</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript" src="/md5.js"></script>
	<link href="/medialink1.png" rel="icon" type="image/x-icon" />
</head>
<body class="bg-white flex justify-center items-center min-h-screen p-2">
        <form method="post" action="<?php echo route('weblogin'); ?>/store">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Form Input</h2>
            <label class="block mb-2">Name:</label>
            <input type="text" name="name" class="w-full px-3 py-2 border rounded mb-4" placeholder="Input your full name" required>

            <label class="block mb-2">Email:</label>
            <input type="email" name="email" class="w-full px-3 py-2 border rounded mb-4" placeholder="Input your email" required>
            
            <label class="block mb-2">Country:</label>
            <input type="text" name="country_id" class="w-full px-3 py-2 border rounded mb-4" />

            <input type="text" name="mac_add" class="w-full px-3 py-2 border rounded mb-4" value="<?php if (isset($data['mac'])) { echo $data['mac']; } ?>" >
            <input type="text" name="useragent" value="{{$data['useragent']}}"/>
            <div class="flex justify-end gap-2">
                <button id="closeModal" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
                <input type="submit" id="submitForm" class="px-4 py-2 bg-green-500 text-white rounded" value="Submit">
            </div>
        </form>
        </div>

    

    <script>
        const modal = document.getElementById('modal');
        const openModal = document.getElementById('openModal');
        const closeModal = document.getElementById('closeModal');
        const submitForm = document.getElementById('submitForm');
        const siginForm = document.getElementById('siginForm');
        const welcomeinfo = document.getElementById('welcomeinfo');
        const mac_add = document.getElementById('mac');
        const errorInfo = document.getElementById('errorInfo');
        const useragent = document.getElementById('useragent');

        let name = document.getElementById('name');
        let option = document.getElementById('country');
        let email = document.getElementById('email');

        openModal.addEventListener('click', async function(e)  {
            e.preventDefault();
            name.value = "";
            option.value = "";
            email.value = "";

            let url = "<?php echo route('country'); ?>";
            try {
                
                const response = await fetch(url);
                const countries = await response.json();
                countries.forEach((country) => {
                    let newOption = document.createElement("option");
                    let optionText = country.country_name;
                    let optionValue = country.id;
                    newOption.text = optionText;
                    newOption.value = optionValue;
                    option.appendChild(newOption);
                });

            } catch (err) {
                console.log(err)
            }
            modal.classList.remove('hidden');
        });
        closeModal.addEventListener('click', () => {
            name.value = "";
            option.value = "";
            email.value = "";
            modal.classList.add('hidden');
        });

        submitForm.addEventListener('click', async function(e) {
            e.preventDefault();

            const country_id = option.value;
            
            const post_url = "<?php echo route('weblogin'); ?>/store";
            await console.log(useragent.value);
            try {

                const response = await fetch(post_url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({name:name.value, email:email.value, country_id:country_id, mac_add:mac_add.value, useragent:useragent.value})
                })
                const result = await response.json();

                //console.log(result);

                document.loginweb.username.value = result.msg.username;
                document.loginweb.password.value = result.msg.password;
                
                if (result.error === true) {
                    let errmsg = "";

                    if (typeof result.msg.name !== "undefined") {
                        errmsg = `<b>${result.msg.name[0]}</b><br>`;
                    }
                    if (typeof result.msg.email !== "undefined") {
                        errmsg = errmsg + `<b>${result.msg.email[0]}</b>`;
                    }
                    
                    errorInfo.innerHTML = errmsg;
                } else {
                    openModal.classList.add('hidden');
                    siginForm.classList.remove('hidden');
                    errorInfo.innerHTML = '';
                    if (result.exist === false) {
                        welcomeinfo.innerHTML = `<b>Welcome ${result.msg.name}</b>`
                    } else {
                        welcomeinfo.innerHTML = `<b>Welcome back ${result.msg.name}</b>`
                    }
                }


            } catch (error) {
                console.log(error);
            }
            modal.classList.add('hidden');
        });

        siginForm.addEventListener('click', () => {
                    <?php if(strlen($data['chap-id']) < 1)  { ?>
                        document.loginweb.submit();
                        return false;
                    <?php }  else { ?>
                        document.loginweb.password.value = hexMD5('<?php echo $data['chap-id']; ?>' + document.loginweb.password.value + '<?php echo $data['chap-challenge']; ?>');
                        document.loginweb.submit();
                        return false;
                  <?php } ?>
        })
    </script>
</body>
</html>
