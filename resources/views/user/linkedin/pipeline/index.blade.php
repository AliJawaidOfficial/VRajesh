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
            max-height: 600px; /* Adjust as needed */
            overflow-y: auto;
            overflow-x: auto; /* Horizontal scrolling */
            scrollbar-width: thin; /* Firefox */
            scrollbar-color: red blue; /* Firefox */
        }

        /* Scrollbar styling for WebKit browsers (Chrome, Safari) */
        #myKanban::-webkit-scrollbar {
            height: 12px; /* Horizontal scrollbar height */
        }

        #myKanban::-webkit-scrollbar-thumb {
            background: red; /* Scrollbar thumb color */
            border-radius: 10px; /* Scrollbar thumb rounded corners */
        }

        #myKanban::-webkit-scrollbar-track {
            background: blue; /* Scrollbar track color */
        }

        .kanban-board {
            background-color: #fff;
            max-height: 500px; /* Adjust as needed */
            overflow-y: auto;
        }

        .kanban-board-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        var kanban = new jKanban({
            element: '#myKanban',
            gutter: '15px',
            widthBoard: '250px',
            responsivePercentage: false,
            dragItems: true,
            boards: [],
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

        // Function to update the item count on a board
        function updateBoardCount(boardId) {
            var boardElement = document.querySelector(`[data-id="${boardId}"]`);
            if (boardElement) {
                var itemCount = boardElement.querySelectorAll('.kanban-item').length;
                var countElement = document.getElementById('count-' + boardId);
                if (countElement) {
                    countElement.innerText = '(' + itemCount + ')';
                }
            }
        }

        function updateAllBoardCounts() {
            kanban.options.boards.forEach(function(board) {
                updateBoardCount(board.id);
            });
        }

        // Function to add a new board
        function addNewBoard() {
            var newBoardTitle = document.getElementById('newBoardTitle').value;
            if (newBoardTitle) {
                var newBoardId = 'board' + (kanban.options.boards.length + 1);
                kanban.addBoards([{
                    id: newBoardId,
                    title: newBoardTitle + ' <span id="count-' + newBoardId +
                        '">(0)</span> <button class="delete-board-btn" onclick="deleteBoard(\'' + newBoardId +
                        '\')">Delete</button><input type="text" class="add-item-input" id="input-' +
                        newBoardId +
                        '" placeholder="New Item Title"><button class="add-item-btn" onclick="addNewItem(\'' +
                        newBoardId + '\')">Add Item</button>',
                    item: []
                }]);
                document.getElementById('newBoardTitle').value = ''; // Clear the input field
                updateBoardCount(newBoardId); // Initialize the item count
            } else {
                alert('Please enter a board title');
            }
        }

        // Function to delete a board
        function deleteBoard(boardId) {
            kanban.removeBoard(boardId);
        }

        // Function to add a new item
        function addNewItem(boardId) {
            var inputId = 'input-' + boardId;
            var newItemTitle = document.getElementById(inputId).value;
            if (newItemTitle) {
                kanban.addElement(boardId, {
                    title: newItemTitle + ' <button class="delete-item-btn" onclick="deleteItem(this, \'' +
                        boardId + '\')">Delete</button>'
                });
                document.getElementById(inputId).value = ''; // Clear the input field
                updateBoardCount(boardId); // Update the item count
            } else {
                alert('Please enter an item title');
            }
        }

        // Function to delete an item
        function deleteItem(button, boardId) {
            var itemElement = button.parentElement;
            var boardElement = document.querySelector(`[data-id="${boardId}"] .kanban-drag`);
            boardElement.removeChild(itemElement);
            updateBoardCount(boardId); // Update the item count
        }

        // Add event listeners to the button
        document.getElementById('addBoardBtn').addEventListener('click', addNewBoard);

        // Initialize board counts
        updateAllBoardCounts();
    </script>
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="h3 p-3 mb-4 bg-white text-center rounded-6">
            <h3 class="mb-0">Published Posts</h3>
        </div>

        <div>
            <div id="boardForm">
                <input type="text" id="newBoardTitle" placeholder="New Board Title">
                <button id="addBoardBtn">Add Board</button>
            </div>
            <div id="myKanban"></div>
        </div>
    </section>
@endsection
