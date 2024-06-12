{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Board')

{{-- Styles --}}
@section('styles')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jkanban@1.3.1/dist/jkanban.min.css">
    <style>
        .board-list-wrapper li a {
            color: black;
        }

        .board-list-wrapper li.active {
            background-color: royalblue;
            border-radius: 6px;
        }

        .board-list-wrapper li.active a {
            color: #fff;
        }

        /* jkanban */
        #boardForm {
            margin: 20px 0;
        }

        #myKanban {
            height: calc(100vh - (20px + 75px + 80px));
            /* max-height: 600px; Adjust as needed */
            overflow-x: auto;
            /* Horizontal scrolling */
            scrollbar-width: thin;
            /* Firefox */
            scrollbar-color: red blue;
            /* Firefox */
        }

        /* Scrollbar styling for WebKit browsers (Chrome, Safari) */
        #myKanban::-webkit-scrollbar {
            height: 12px;
            /* Horizontal scrollbar height */
        }

        #myKanban::-webkit-scrollbar-thumb {
            background: red;
            /* Scrollbar thumb color */
            border-radius: 10px;
            /* Scrollbar thumb rounded corners */
        }

        #myKanban::-webkit-scrollbar-track {
            background: blue;
            /* Scrollbar track color */
        }

        .kanban-container {
            height: 100%;
        }

        .kanban-container>div:nth-child(1) {
            margin-left: 0px !important;
        }

        .kanban-container>div:last-child {
            margin-right: 0px !important;
        }

        .kanban-board {
            background-color: #fff;
            border-radius: 3px;
            max-height: 100%;
            /* Adjust as needed */
            overflow-y: auto;
        }

        .kanban-board-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: column;
        }

        .delete-board-btn,
        .add-item-btn,
        .delete-item-btn {
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }

        .add-item-input {
            width: 60%;
            margin-right: 10px;
        }

        .kanban-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
@endsection

{{-- Vendor Scripts --}}
@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/jkanban@1.3.1/dist/jkanban.min.js"></script>
    <script>
        var kanban;
        var boardsData = []; // Store the boards data

        function generateRandomId() {
            return 'id-' + Math.random().toString(36).substr(2, 9);
        }

        function initializeKanban() {
            kanban = new jKanban({
                element: '#myKanban',
                gutter: '15px',
                widthBoard: '250px',
                responsivePercentage: false,
                dragItems: true,
                boards: boardsData,
                dragEl: function(el, source) {
                    console.log('START DRAG: ' + el.dataset.eid);
                },
                dragendEl: function(el) {
                    console.log('END DRAG: ' + el.dataset.eid);
                    updateAllBoardCounts();
                },
                dropEl: function(el, target, source, sibling) {
                    console.log('DROPPED: ' + el.dataset.eid);
                    updateAllBoardCounts();
                }
            });
            updateAllBoardCounts();
        }

        function updateBoardCount(boardId) {
            var boardElement = document.querySelector(`[data-id="${boardId}"]`);
            if (boardElement) {
                var itemCount = boardElement.querySelectorAll('.kanban-item').length;
                var countElement = boardElement.querySelector(`#count-${boardId}`);
                if (countElement) {
                    countElement.innerText = `(${itemCount})`;
                }
            }
        }

        function updateAllBoardCounts() {
            kanban.options.boards.forEach(function(board) {
                updateBoardCount(board.id);
            });
        }

        function addNewBoard() {
            var newBoardTitle = document.getElementById('newBoardTitle').value;
            if (newBoardTitle) {
                var newBoardId = generateRandomId();
                var newBoard = {
                    id: newBoardId,
                    title: `
                    <div class="d-flex">
                        <span>${newBoardTitle}</span> 
                        <span id="count-${newBoardId}">(0)</span>
                        <button class="delete-board-btn" onclick="deleteBoard('${newBoardId}')"><i class="fas fa-trash d-inline-block me-1"></i> Delete</button>
                        <button class="add-item-btn" data-board-id="${newBoardId}" data-bs-toggle="modal" data-bs-target="#updateListModal">Add Item</button>
                    </div>
                    `,
                    item: []
                };
                boardsData.push(newBoard);
                document.getElementById('newBoardTitle').value = ''; // Clear the input field
                reRenderKanban(); // Re-render the Kanban board
            } else {
                alert('Please enter a board title');
            }
        }

        function deleteBoard(boardId) {
            boardsData = boardsData.filter(board => board.id !== boardId);
            reRenderKanban(); // Re-render the Kanban board
        }

        function addNewItem(boardId) {
            var inputId = 'input-' + boardId;
            var newItemTitle = document.getElementById(inputId).value;
            if (newItemTitle) {
                var board = boardsData.find(board => board.id === boardId);
                if (board) {
                    var newItemId = generateRandomId();
                    board.item.push({
                        id: newItemId,
                        title: `${newItemTitle} <button class="delete-item-btn" onclick="deleteItem('${newItemId}', '${boardId}')"><i class="fas fa-trash d-inline-block me-1"></i> Delete</button>`
                    });
                    document.getElementById(inputId).value = ''; // Clear the input field
                    kanban.addElement(boardId, {
                        id: newItemId,
                        title: `${newItemTitle} <button class="delete-item-btn" onclick="deleteItem('${newItemId}', '${boardId}')"><i class="fas fa-trash d-inline-block me-1"></i> Delete</button>`
                    });
                    updateBoardCount(boardId); // Update the board count
                }
            } else {
                alert('Please enter an item title');
            }
        }

        function deleteItem(itemId, boardId) {
            var board = boardsData.find(board => board.id === boardId);
            if (board) {
                var itemIndex = board.item.findIndex(item => item.id === itemId);
                if (itemIndex > -1) {
                    board.item.splice(itemIndex, 1);
                    kanban.removeElement(itemId);
                    updateBoardCount(boardId); // Update the board count
                }
            }
        }

        function reRenderKanban() {
            // Destroy the current instance by clearing the container
            var kanbanContainer = document.getElementById('myKanban');
            kanbanContainer.innerHTML = '';

            // Reinitialize with updated data
            initializeKanban();
        }

        // Set the board ID in the modal when the "Add Item" button is clicked
        document.addEventListener('click', function(event) {
            if (event.target.matches('.add-item-btn')) {
                var boardId = event.target.getAttribute('data-board-id');
                document.querySelector('#updateListModal').setAttribute('data-board-id', boardId);
            }
        });

        // Add event listener to the button
        document.getElementById('addBoardBtn').addEventListener('click', addNewBoard);

        // Initialize Kanban for the first time
        initializeKanban();

        // Add event listener to the modal form
        document.getElementById('addNewItemForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Get the new item title and board ID from the form and modal attributes
            var newItemTitle = document.getElementById('newItemTitle').value;
            var boardId = document.querySelector('#updateListModal').getAttribute('data-board-id');

            if (newItemTitle) {
                // Find the board using the board ID
                var board = boardsData.find(board => board.id === boardId);
                if (board) {
                    var newItemId = generateRandomId();
                    // Add the new item to the board's item list
                    board.item.push({
                        id: newItemId,
                        title: `${newItemTitle} <button class="delete-item-btn" onclick="deleteItem('${newItemId}', '${boardId}')"><i class="fas fa-trash d-inline-block me-1"></i> Delete</button>`
                    });
                    // Clear the input field and close the modal
                    document.getElementById('newItemTitle').value = '';
                    $('#updateListModal').modal('hide');
                    // Add the new item to the Kanban board
                    kanban.addElement(boardId, {
                        id: newItemId,
                        title: `${newItemTitle} <button class="delete-item-btn" onclick="deleteItem('${newItemId}', '${boardId}')"><i class="fas fa-trash d-inline-block me-1"></i> Delete</button>`
                    });
                    updateBoardCount(boardId); // Update the board count
                }
            } else {
                alert('Please enter an item title');
            }
        });
    </script>
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div id="boardForm">
            <input type="text" id="newBoardTitle" placeholder="New Board Title">
            <button id="addBoardBtn">Add Board</button>
        </div>
        <div id="myKanban"></div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="updateListModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Add New Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addNewItemForm">
                        <div class="mb-3">
                            <label for="newItemTitle" class="form-label">New Item Title</label>
                            <input type="text" class="form-control" id="newItemTitle" placeholder="Enter item title">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
