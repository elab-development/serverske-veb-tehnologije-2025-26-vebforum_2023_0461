import client from "./client";

export function listComments(postId) {
  return client.get(`/posts/${postId}/comments`).then((res) => res.data);
}

export function createComment(postId, data) {
  return client
    .post(`/posts/${postId}/comments`, data)
    .then((res) => res.data);
}

export function updateComment(id, data) {
  return client.put(`/comments/${id}`, data).then((res) => res.data);
}

export function deleteComment(id) {
  return client.delete(`/comments/${id}`).then((res) => res.data);
}

export function toggleCommentLike(id) {
  return client.post(`/comments/${id}/like`).then((res) => res.data);
}
