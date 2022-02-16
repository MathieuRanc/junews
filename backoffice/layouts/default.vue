<template>
	<div>
		<Nuxt />
	</div>
</template>

<style lang="scss">
textarea,
input[type='text'],
input[type='button'],
input[type='submit'] {
	-webkit-appearance: none;
	border-radius: 0;
}
html {
	font-size: 16px;
	word-spacing: 1px;
	-ms-text-size-adjust: 100%;
	-webkit-text-size-adjust: 100%;
	-moz-osx-font-smoothing: grayscale;
	-webkit-font-smoothing: antialiased;
	box-sizing: border-box;
}
textarea,
input[type='datetime-local'],
html {
	font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont,
		'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

*,
*::before,
*::after {
	box-sizing: border-box;
	margin: 0;
}
table {
	border-spacing: 0;
	border-radius: 10px;
	background-color: #fff;
	margin-top: 20px;
	padding: 0 20px;
	width: 100%;
	img {
		height: 60px;
		width: 60px;
		object-fit: contain;
		filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.2));
	}
	td,
	th {
		padding: 20px 8px;
		text-align: center;
		&:nth-child(1),
		&:nth-child(2) {
			text-align: left;
		}
	}
	th,
	tr:not(:last-child) td {
		border-bottom: 1px solid rgb(216, 216, 216);
	}
	td {
		min-width: 16px;
		max-width: 200px;
		text-overflow: ellipsis;
		white-space: nowrap;
		overflow: hidden;
	}
}
button {
	border: none;
	outline: none;
	border-radius: 4px;
	padding: 10px;
	cursor: pointer;
	transition: background-color 200ms;
}
.modify {
	background-color: rgb(0, 116, 189);
	color: white;
	&:hover {
		background-color: rgb(0, 105, 171);
	}
}
.delete {
	background-color: rgb(205, 69, 69);
	color: white;
	&:hover {
		background-color: rgb(176, 58, 58);
	}
}
.popup {
	z-index: 1;
	position: fixed;
	overflow: scroll;
	top: 0;
	left: 0;
	bottom: 0;
	right: 0;
	width: 100vw;
	min-height: 100vh;
	padding: auto;
	background-color: rgba(0, 0, 0, 0.6);
	.box {
		margin: 40px 0;
		position: absolute;
		width: 90%;
		padding: 40px;
		background-color: white;
		border-radius: 10px;
		top: 0;
		left: 50%;
		transform: translateX(-50%);
		display: flex;
		flex-direction: column;
		img {
			margin-bottom: 8px;
		}
		.header {
			display: flex;
		}
		h2 {
			margin-bottom: 20px;
		}
	}
	.close {
		position: absolute;
		top: 20px;
		right: 20px;
		border-radius: 30px;
		width: 40px;
		height: 40px;
		background-color: rgb(205, 69, 69);
		color: white;
		font-size: 20px;
		&:hover {
			background-color: rgb(176, 58, 58);
		}
	}
	.validate {
		background-color: rgb(24, 123, 24);
		color: white;
		&:hover {
			background-color: rgb(38, 136, 38);
		}
	}
	textarea {
		resize: vertical;
	}
	input,
	textarea,
	select {
		border: none;
		margin-bottom: 20px;
		border-radius: 4px;
		padding: 8px;
		border: none;
		outline: none;
		transition: box-shadow 200ms;
		box-shadow: -2px 2px 10px rgba(0, 0, 0, 0.1);
		&:focus,
		&:hover {
			box-shadow: -4px 4px 10px rgba(0, 0, 0, 0.2);
		}
	}
	label {
		margin-bottom: 4px;
		font-weight: 500;
		margin-right: auto;
		cursor: pointer;
	}
	.tags {
		padding: 0;
		display: flex;
		flex-wrap: wrap;
		list-style: none;
		li {
			cursor: pointer;
			margin-bottom: 10px;
			border-radius: 30px;
			transition: background-color 200ms;
			background-color: rgb(0, 150, 230);
			&:hover {
				background-color: rgb(0, 130, 230);
			}
			color: white;
			padding: 10px 12px;
			font-size: 12px;
			margin-right: 6px;
		}
	}
}
.creer {
	margin-top: 20px;
	background-color: white;
	border-radius: 10px;
	button.create {
		display: flex;
		align-items: center;
		background-color: transparent;
		width: 100%;
		transition: box-shadow 200ms;
		&:hover {
			box-shadow: -4px 4px 10px rgba(128, 128, 128, 0.2);
		}
		img {
			margin-right: 10px;
		}
	}
}
</style>
