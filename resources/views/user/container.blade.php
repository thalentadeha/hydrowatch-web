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
                                    <td>{{ isset($containerData['weight']) ? (String) $containerData['weight']: 'Not set!' }}
                                    </td>
                                    <td>{{ isset($containerData['volume']) ? (String) $containerData['volume']: 'Not set!' }}
                                    </td>
                                    <td>{{ isset($containerData['description']) ? $containerData['description'] : '' }}
                                    </td>
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
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id1">
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id2">
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id3">
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id4">
                        </div>
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
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id1">
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id2">
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id3">
                            <span>:</span>
                            <input type="text" placeholder="AA" maxlength="2" name="nfc_id4">
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

            let button = document.querySelector('dialog form button[type="submit"]');
            button.disabled = true;

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
                        button.disabled = false;
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
                const weight = 'Not set!';
                const volume = 'Not set!';
                const description = formData.get('containerDesc') === "" || formData.get('containerDesc') === null ? "" : formData.get('containerDesc');

                const containerList = document.querySelector('.table-data #container-list tbody');
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

                        if(currentNfcid.toLowerCase() === "No container found.".toLowerCase()) {
                            containerList.removeChild(currentRow);
                        }

                        else if (nfcid_uppercase < currentNfcid) {
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

                    if(rowsArray.length === 1) {
                        const emptyRow = document.createElement('tr');
                        emptyRow.innerHTML = `<td colspan="4">No container found.</td>`;
                        containerList.append(emptyRow);
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
