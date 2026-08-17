import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import * as postsApi from "../api/posts";
import * as commentsApi from "../api/comments";
import { useAuth } from "../context/AuthContext";
import { ErrorMessage } from "./StatusMessage";

export default function PostThread({ post, onChanged }) {
  const { user, isAuthenticated, isModerator } = useAuth();
  const [comments, setComments] = useState([]);
  const [commentsLoaded, setCommentsLoaded] = useState(false);
  const [newComment, setNewComment] = useState("");
  const [editing, setEditing] = useState(false);
  const [editBody, setEditBody] = useState(post.body);
  const [liked, setLiked] = useState(null);
  const [error, setError] = useState(null);

  const canModeratePost =
    isAuthenticated && (isModerator || post.user_id === user?.id);

  function loadComments() {
    commentsApi
      .listComments(post.id)
      .then((res) => {
        setComments(res.data);
        setCommentsLoaded(true);
      })
      .catch(setError);
  }

  useEffect(() => {
    if (isAuthenticated) loadComments();
  }, [post.id, isAuthenticated]);

  async function handleToggleLike() {
    try {
      const res = await postsApi.togglePostLike(post.id);
      setLiked(res.liked);
    } catch (err) {
      setError(err);
    }
  }

  async function handleSaveEdit() {
    try {
      await postsApi.updatePost(post.id, { body: editBody, topic_id: post.topic_id });
      setEditing(false);
      onChanged();
    } catch (err) {
      setError(err);
    }
  }

  async function handleDeletePost() {
    if (!confirm("Obrisati post?")) return;
    try {
      await postsApi.deletePost(post.id);
      onChanged();
    } catch (err) {
      setError(err);
    }
  }

  async function handleAddComment(e) {
    e.preventDefault();
    if (!newComment.trim()) return;
    try {
      await commentsApi.createComment(post.id, { body: newComment });
      setNewComment("");
      loadComments();
    } catch (err) {
      setError(err);
    }
  }

  return (
    <div className="post-thread">
      <p className="post-meta">Korisnik #{post.user_id}</p>
      {editing ? (
        <div className="form">
          <textarea
            rows={3}
            value={editBody}
            onChange={(e) => setEditBody(e.target.value)}
          />
          <div className="card-actions">
            <button type="button" onClick={handleSaveEdit}>
              Sačuvaj
            </button>
            <button type="button" onClick={() => setEditing(false)}>
              Otkaži
            </button>
          </div>
        </div>
      ) : (
        <p className="post-body">{post.body}</p>
      )}

      <div className="card-actions">
        <button type="button" disabled={!isAuthenticated} onClick={handleToggleLike}>
          {liked ? "♥ Sviđa mi se" : "♡ Sviđa mi se"}
        </button>
        {canModeratePost && !editing && (
          <>
            <button type="button" onClick={() => setEditing(true)}>
              Izmeni
            </button>
            <button type="button" onClick={handleDeletePost}>
              Obriši
            </button>
          </>
        )}
      </div>

      <ErrorMessage error={error} />

      <div className="comments">
        {isAuthenticated ? (
          <>
            {commentsLoaded && comments.length === 0 && (
              <p className="muted">Nema komentara.</p>
            )}
            {comments.map((comment) => (
              <CommentItem
                key={comment.id}
                comment={comment}
                onChanged={loadComments}
              />
            ))}
            <form onSubmit={handleAddComment} className="form form-inline">
              <input
                placeholder="Dodaj komentar…"
                value={newComment}
                onChange={(e) => setNewComment(e.target.value)}
              />
              <button type="submit">Pošalji</button>
            </form>
          </>
        ) : (
          <p className="muted">
            <Link to="/login">Prijavi se</Link> da vidiš i dodaš komentare.
          </p>
        )}
      </div>
    </div>
  );
}

function CommentItem({ comment, onChanged }) {
  const { user, isModerator } = useAuth();
  const [editing, setEditing] = useState(false);
  const [body, setBody] = useState(comment.body);
  const [liked, setLiked] = useState(null);
  const [error, setError] = useState(null);

  const canModerate = isModerator || comment.user_id === user?.id;

  async function handleToggleLike() {
    try {
      const res = await commentsApi.toggleCommentLike(comment.id);
      setLiked(res.liked);
    } catch (err) {
      setError(err);
    }
  }

  async function handleSave() {
    try {
      await commentsApi.updateComment(comment.id, { body });
      setEditing(false);
      onChanged();
    } catch (err) {
      setError(err);
    }
  }

  async function handleDelete() {
    if (!confirm("Obrisati komentar?")) return;
    try {
      await commentsApi.deleteComment(comment.id);
      onChanged();
    } catch (err) {
      setError(err);
    }
  }

  return (
    <div className="comment-item">
      <p className="comment-meta">{comment.user?.name || `Korisnik #${comment.user_id}`}</p>
      {editing ? (
        <div className="form-inline">
          <input value={body} onChange={(e) => setBody(e.target.value)} />
          <button type="button" onClick={handleSave}>
            Sačuvaj
          </button>
          <button type="button" onClick={() => setEditing(false)}>
            Otkaži
          </button>
        </div>
      ) : (
        <p>{comment.body}</p>
      )}
      <div className="card-actions">
        <button type="button" onClick={handleToggleLike}>
          {liked ? "♥" : "♡"}
        </button>
        {canModerate && !editing && (
          <>
            <button type="button" onClick={() => setEditing(true)}>
              Izmeni
            </button>
            <button type="button" onClick={handleDelete}>
              Obriši
            </button>
          </>
        )}
      </div>
      <ErrorMessage error={error} />
    </div>
  );
}
