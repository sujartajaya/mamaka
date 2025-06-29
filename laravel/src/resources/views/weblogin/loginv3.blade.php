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
    <div class="max-w-sm w-full bg-indigo-800 p-6 rounded-lg text-center text-white shadow-lg">
        <h1 class="text-lg font-bold uppercase">Kuta Social Club</h1>
        <h2 class="text-2xl font-bold mt-2">Let's Connect!</h2>
        
        <div class="my-4 flex justify-center">
            <img src="{{ route('home') }}/logo.svg" alt="Kuta Social Club" width="200" heigth="200">
        </div>

        <p class="text-sm font-semibold">FREE INTERNET ACCESS</p>
        <p class="text-xs mt-2 px-2" id="welcomeinfo" ><?php if ($guest) { ?>  <b>Welcome back {{ $guest['name'] }}</b><?php } else {?>By clicking the "Fill Form" button, you consent to receive marketing, promotional messages and information about Kuta Social Club and its affiliated hotel network (Ovolo, By Ovolo Collective). You may opt out of communications at any time. All data obtained is subject to the Privacy Policy.<?php } ?></p>

        <button id="openModal" class="bg-teal-400 text-indigo-900 font-bold px-6 py-2 rounded-lg mt-4 w-full <?php if ($guest) { echo "hidden"; } ?>">Fill Form</button>
        <button id="siginForm" class="bg-teal-400 text-indigo-900 font-bold px-6 py-2 rounded-lg mt-4 w-full <?php if (!$guest) { echo "hidden"; } ?>">Sign-in</button>

        <div class="mt-6 text-sm">
            <p id="errorInfo" class="font-bold text-red-800">{{ $data['error'] }}</p>
        </div>
        <div class="mt-6 text-sm">
            <p class="font-bold">Jalan Pantai Kuta</p>
            <p>No.32 Kuta, Bali</p>
            <p>80361 - Indonesia</p>
        </div>
        
        <div class="mt-4 space-y-2 text-sm">
            <p>☎ +62 811 3810 0032</p>
            <p>📷 mamakabali</p>
            <p>🌐 mamakabyoovolo.com</p>
        </div>
    </div>

    <div id="modal" class="fixed inset-0 flex items-center justify-center bg-gray-700 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Form Input</h2>
            <label class="block mb-2">Name:</label>
            <input type="text" id="name" class="w-full px-3 py-2 border rounded mb-4" placeholder="Input your full name" required>

            <label class="block mb-2">Email:</label>
            <input type="email" id="email" class="w-full px-3 py-2 border rounded mb-4" placeholder="Input your email" required>
            
            <label class="block mb-2">Country:</label>
            <select id="country" class="w-full px-3 py-2 border rounded mb-4">
            </select>

            <input type="hidden" id="mac" class="w-full px-3 py-2 border rounded mb-4" value="<?php if (isset($data['mac'])) { echo $data['mac']; } ?>" >
            
            <div class="flex justify-end gap-2">
                <button id="closeModal" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
                <button id="submitForm" class="px-4 py-2 bg-green-500 text-white rounded">Submit</button>
            </div>
        </div>
    </div>

    <form name="loginweb" action="<?php if (isset($data['link-login'])) { echo $data['link-login']; } ?>" method="POST">
        <input type="hidden" name="username" value="<?php if($guest) { echo $guest['username']; }?>" />
        <input type="hidden" name="password" value="<?php if($guest) { echo $guest['password']; }?>" />
        <input type="hidden" name="dst" value="https://ovolohotels.com/mamaka/long-stay/?gad_source=1&gad_campaignid=10952323866&gbraid=0AAAAADv4kheTZBXGJ3XMD38kkB7ImgCQD&gclid=Cj0KCQjwjdTCBhCLARIsAEu8bpI54UUiTSvQEVr5cIV1WbWTi7Cz5CuVRrphyl-Xlx3sKDEYi9eqx6oaAiglEALw_wcB" />
        <input type="hidden" name="popup" value="true" />
    </form>

    <script>
        const modal = document.getElementById('modal');
        const openModal = document.getElementById('openModal');
        const closeModal = document.getElementById('closeModal');
        const submitForm = document.getElementById('submitForm');
        const siginForm = document.getElementById('siginForm');
        const welcomeinfo = document.getElementById('welcomeinfo');
        const mac_add = document.getElementById('mac');
        const errorInfo = document.getElementById('errorInfo');

        let name = document.getElementById('name');
        let option = document.getElementById('country');
        let email = document.getElementById('email');

        // @if($guest)
        //     @if(strlen($data['chap-id']) < 1)
        //         document.loginweb.submit();
        //         return false;
        //     @else
        //         document.loginweb.password.value = hexMD5('<?php echo $data['chap-id']; ?>' + document.loginweb.password.value + '<?php echo $data['chap-challenge']; ?>');
        //         document.loginweb.submit();
        //         return false;
        //     @endif
        // @endif

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
            try {

                const response = await fetch(post_url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({name:name.value, email:email.value, country_id:country_id, mac_add:mac_add.value})
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
                    // openModal.classList.add('hidden');
                    // siginForm.classList.remove('hidden');
                    // errorInfo.innerHTML = '';
                    // if (result.exist === false) {
                    //     welcomeinfo.innerHTML = `<b>Welcome ${result.msg.name}</b>`
                    // } else {
                    //     welcomeinfo.innerHTML = `<b>Welcome back ${result.msg.name}</b>`
                    // }

                    @if(strlen($data['chap-id']) < 1)
                        document.loginweb.submit();
                        return false;
                    @else
                        document.loginweb.password.value = hexMD5('<?php echo $data['chap-id']; ?>' + document.loginweb.password.value + '<?php echo $data['chap-challenge']; ?>');
                        document.loginweb.submit();
                        return false;
                    @endif
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
