{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Board')

{{-- Styles --}}
@section('styles')
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

        .kanban-board {
            display: flex;
            gap: 15px;
            padding: 20px;
        }

        .kanban-column {
            background-color: #f4f4f4;
            border-radius: 8px;
            padding: 15px;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .kanban-column h4 {
            margin-bottom: 15px;
        }

        .kanban-task {
            background-color: #fff;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: move;
        }

        .create-task {
            margin-top: auto;
        }
    </style>
@endsection

{{-- Scripts --}}
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createBoardButton = document.querySelector('.create-board-wrapper button');
            const boardListWrapper = document.querySelector('.board-list-wrapper');
            const kanbanBoard = document.querySelector('.kanban-board');

            createBoardButton.addEventListener('click', function() {
                const input = document.querySelector('.create-board-wrapper input');
                const boardName = input.value.trim();
                if (boardName) {
                    const newBoard = document.createElement('li');
                    newBoard.classList.add('py-2', 'px-3');
                    newBoard.innerHTML = `<a href="#" class="text-decoration-none">${boardName}</a>`;
                    boardListWrapper.appendChild(newBoard);
                    input.value = '';
                }
            });

            boardListWrapper.addEventListener('click', function(event) {
                if (event.target.tagName === 'A') {
                    const boards = boardListWrapper.querySelectorAll('li');
                    boards.forEach(board => board.classList.remove('active'));
                    event.target.parentElement.classList.add('active');
                    // Load the corresponding Kanban board here
                }
            });

            function createKanbanColumn(title) {
                const column = document.createElement('div');
                column.classList.add('kanban-column');
                column.innerHTML = `<h4>${title}</h4><div class="kanban-tasks"></div><button class="btn btn-light create-task">+ Add Task</button>`;
                kanbanBoard.appendChild(column);

                const addTaskButton = column.querySelector('.create-task');
                addTaskButton.addEventListener('click', function() {
                    const taskText = prompt('Enter task description:');
                    if (taskText) {
                        const task = document.createElement('div');
                        task.classList.add('kanban-task');
                        task.textContent = taskText;
                        column.querySelector('.kanban-tasks').appendChild(task);
                        // Make the task draggable
                        makeTaskDraggable(task);
                    }
                });
            }

            function makeTaskDraggable(task) {
                task.draggable = true;
                task.addEventListener('dragstart', function() {
                    task.classList.add('dragging');
                });
                task.addEventListener('dragend', function() {
                    task.classList.remove('dragging');
                });
            }

            function makeColumnDroppable(column) {
                column.addEventListener('dragover', function(event) {
                    event.preventDefault();
                    const draggingTask = document.querySelector('.dragging');
                    const tasksContainer = column.querySelector('.kanban-tasks');
                    tasksContainer.appendChild(draggingTask);
                });
            }

            // Example usage
            ['To Do', 'In Progress', 'Done'].forEach(columnTitle => {
                const column = createKanbanColumn(columnTitle);
                makeColumnDroppable(column);
            });
        });
    </script>
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="h3 p-3 mb-4 bg-white text-center rounded-8">
            <h3 class="mb-0">Published Posts</h3>
        </div>

        <div class="row gap-3" style="padding: 0px 13px">
            <div class="col-md-3 bg-white rounded-8 p-3">
                <div class="create-board-wrapper d-flex align-items-stretch justify-content-between gap-2">
                    <input type="text" class="flex-grow-1 form-control" placeholder="Create Board">
                    <button class="btn btn-primary">+</button>
                </div>

                <ul class="list-unstyled mt-3 board-list-wrapper">
                    <li class="py-2 px-3 active"><a href="#" class="text-decoration-none">Board 1</a></li>
                    <li class="py-2 px-3"><a href="#" class="text-decoration-none">Board 2</a></li>
                    <li class="py-2 px-3"><a href="#" class="text-decoration-none">Board 3</a></li>
                </ul>
            </div>
            <div class="col-md-9 bg-white rounded-8">
                <div class="kanban-board"></div>
            </div>
        </div>
    </section>
@endsection
