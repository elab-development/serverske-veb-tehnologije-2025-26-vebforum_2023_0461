import client from "./client";

export function listPosts(params = {}) {
  return client.get("/posts", { params }).then((res) => res.data);
}

export function getPost(id) {
  return client.get(`/posts/${id}`).then((res) => res.data);
}

export function createPost(data) {
  return client.post("/posts", data).then((res) => res.data);
}

export function updatePost(id, data) {
  return client.put(`/posts/${id}`, data).then((res) => res.data);
}

export function deletePost(id) {
  return client.delete(`/posts/${id}`).then((res) => res.data);
}

export function togglePostLike(id) {
  return client.post(`/posts/${id}/like`).then((res) => res.data);
}
