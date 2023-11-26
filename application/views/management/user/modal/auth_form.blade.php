<div class="modal fade" id="auth-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close auth-modal-close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">권한설정</h4>
			</div>
			<div class="modal-body">
				<div id="auth-modal-contents">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="modal-save-btn" class="btn btn-info">저장</button>
				<button type="button" class="btn btn-default auth-modal-close" data-dismiss="modal">닫기</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(".auth-modal-close").click(() => {
		$("#auth-modal").modal('hide')
	})
</script>
