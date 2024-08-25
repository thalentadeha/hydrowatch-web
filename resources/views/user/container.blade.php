@extends('user.layout')

@section('content')
    <main class="container">
        <section>
            <div class="title">
                <h1>Container List</h1>
                <div class="buttons">
                    <form action="">
                        <button type="button" class="AddContainer blue">New Container</button>
                        <button type="button" class="DeleteContainer red">Delete Container</button>
                    </form>
                </div>
            </div>
            <div class="table-data">
                <table id="container-list">
                    <thead>
                        <tr>
                            <th>Container ID</th>
                            <th>Weight (gram)</th>
                            <th>Volume (mL)</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($containerList))
                            @foreach ($containerList as $nfcid => $containerData)
                                <tr>
                                    <td>{{ $nfcid }}</td>
                                    <td>{{ $containerData['weight'] !== -1 ? $containerData['weight'] : 'set weight at dispenser!' }}
                                    </td>
                                    <td>{{ $containerData['volume'] !== -1 ? $containerData['volume'] : 'set volume at dispenser!' }}
                                    </td>
                                    <td>{{ $containerData['description'] }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">No container found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection

@section('script')
    <script>
        const AddContainer = document.querySelector(".AddContainer")
        if (AddContainer !== null) {
            AddContainer.addEventListener("click", () => {
                showDialogBox("AddContainer")
            })
        }

        const DeleteContainer = document.querySelector(".DeleteContainer")
        if (DeleteContainer !== null) {
            DeleteContainer.addEventListener("click", () => {
                showDialogBox("DeleteContainer")
            })
        }

        function showDialogBox(target) {
            const dialogBox = document.querySelector("dialog")
            const innerDialog = getDialogBoxContent(target)
            dialogBox.innerHTML = innerDialog
            if (innerDialog !== ``) {
                dialogBox.show()
                dialogBox.querySelector(".content .text-area .close").addEventListener("click", () => {
                    dialogBox.close()
                })
            }

            const form = dialogBox.querySelector('form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                submitForm(new FormData(form), target);
            });
        }

        function getDialogBoxContent(target) {
            switch (target) {
                case "AddContainer":
                    return `
                <div class="content">
                    <div class="text-area">
                        <span>New Container</span>
                        <img class="close" src="{{ asset('img/close.png') }}" alt="">
                    </div>
                    <form action="{{ route('addContainer_POST') }}" method="POST">
                        @csrf
                        <div class="nfc">
                            <span>NFC ID</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id1" required>
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id2" required>
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id3" required>
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id4" required>
                        </div>
                        {{-- <div class="container">
                            <span>Container Weight</span>
                            <input type="number" placeholder="gram" name="weight" required>
                            <span>Container Volume</span>
                            <input type="number" placeholder="(100 - 6000)mL" name="volume" required>
                        </div> --}}
                        <textarea name="containerDesc" class="desc" placeholder="Container Description"></textarea>
                        <input type="hidden" name="idToken" value="{{ session('idToken') }}">
                        <button type="submit" class="addContainer blue">Save Container</button>
                    </form>
                </div>
            `
                case "DeleteContainer":
                    return `
                <div class="content">
                    <div class="text-area">
                        <span>Delete Container</span>
                        <img class="close" src="{{ asset('img/close.png') }}" alt="">
                    </div>
                    <form action="{{ route('deleteContainer_POST') }}" method="POST">
                        @csrf
                        <div class="nfc">
                            <span>NFC ID</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id1" required>
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id2" required>
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id3" required>
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id4" required>
                        </div>
                        <input type="hidden" name="idToken" value="{{ session('idToken') }}">
                        <button type="submit" class="deleteContainer red">Delete Container</button>
                    </form>
                </div>
            `
            }
        }

        function submitForm(formData, target) {
            let ACTION_URL;

            switch (target) {
                case "AddContainer":
                    ACTION_URL = '{{ route('addContainer_POST') }}';
                    break;
                case "DeleteContainer":
                    ACTION_URL = '{{ route('deleteContainer_POST') }}';
                    break;
            }


            fetch(ACTION_URL, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                }).then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.querySelector('dialog').close();
                        alert(data.success);

                        updateData(formData, target);
                    }
                })
                .catch(error => {
                    if (error.errors) {
                        showErrors(error.errors);
                    }
                });
        }

        function updateData(formData, target) {
            const nfcid = formData.get('nfc_id1') + ":" +
                formData.get('nfc_id2') + ":" +
                formData.get('nfc_id3') + ":" +
                formData.get('nfc_id4');
            const nfcid_uppercase = nfcid.toUpperCase();

            if (target === "AddContainer") {
                const weight = 'set weight at dispenser!';
                const volume = 'set volume at dispenser!';
                const description = formData.get('containerDesc') != null ? formData.get('description') : '';

                // console.log('NFC ID:', nfcid);
                // console.log('Weight:', weight);
                // console.log('Volume:', volume);
                // console.log('Description:', description);

                const containerList = document.querySelector('.table-data #container-list tbody');
                // console.log('containerList:', containerList);
                if (containerList) {
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = '<td>' + nfcid_uppercase + '</td>' +
                        '<td>' + weight + '</td>' +
                        '<td>' + volume + '</td>' +
                        '<td>' + description + '</td>';

                    // Get existing rows and convert to array
                    const rowsArray = Array.from(containerList.querySelectorAll('tr'));

                    // Insert the new row in the correct position
                    let inserted = false;
                    for (let i = 0; i < rowsArray.length; i++) {
                        const currentRow = rowsArray[i];
                        const currentNfcid = currentRow.cells[0].textContent.toUpperCase();

                        if (nfcid_uppercase < currentNfcid) {
                            containerList.insertBefore(newRow, currentRow);
                            inserted = true;
                            break;
                        }
                    }

                    // If the new row is not inserted (meaning it is the last row), append it
                    if (!inserted) {
                        containerList.appendChild(newRow);
                    }
                }
            } else {
                const containerList = document.querySelector('.table-data #container-list tbody');
                if (containerList) {
                    const rowsArray = Array.from(containerList.querySelectorAll('tr'));

                    // Find and delete the row with the matching NFC ID
                    for (let i = 0; i < rowsArray.length; i++) {
                        const currentRow = rowsArray[i];
                        const currentNfcid = currentRow.cells[0].textContent.toUpperCase();

                        if (nfcid_uppercase === currentNfcid) {
                            containerList.removeChild(currentRow);
                            break;
                        }
                    }
                }
            }
        }

        function showErrors(error) {
            const existingWarning = document.querySelector('.warning-form');
            if (existingWarning) {
                existingWarning.remove();
            }
            if (error) {
                const form = document.querySelector('form');
                const errorSpan = document.createElement('span');
                errorSpan.className = 'warning-form';
                errorSpan.textContent = error;
                form.appendChild(errorSpan);
            }
        }
    </script>
@endsection

<style>
    .container {
        display: flex;
        gap: 10px;
    }

    .container span {
        font-size: var(--s7);
        color: var(--color-primary);
        font-family: var(--font-semibold);
    }

    .container input {
        flex: 1;
        padding: 10px;
        margin: 5px;
        box-sizing: border-box;
    }
</style>
