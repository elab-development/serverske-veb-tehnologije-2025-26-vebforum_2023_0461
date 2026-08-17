import { useEffect, useState } from "react";
import { Link, useNavigate, useParams } from "react-router-dom";
import * as topicsApi from "../api/topics";
import * as postsApi from "../api/posts";
import { useAuth } from "../context/AuthContext";
import { ErrorMessage, LoadingSpinner } from "../components/StatusMessage";
import PostThread from "../components/PostThread";

export default function TopicDetailPage() {
  const { id } = useParams();
  const { user, isAuthenticated, isModerator } = useAuth();
  const navigate = useNavigate();

  const [topic, setTopic] = useState(null);
  const [posts, setPosts] = useState([]);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(true);
  const [replyBody, setReplyBody] = useState("");
  const [posting, setPosting] = useState(false);
  const [voteBusy, setVoteBusy] = useState(false);

  function load() {
    setLoading(true);
    setError(null);
    Promise.all([topicsApi.getTopic(id), topicsApi.listTopicPosts(id)])
      .then(([topicRes, postsRes]) => {
        setTopic(topicRes.data);
        setPosts(postsRes);
      })
      .catch(setError)
      .finally(() => setLoading(false));
  }

  useEffect(load, [id]);

  const canModerateTopic =
    isAuthenticated && (isModerator || topic?.user_id === user?.id);

  async function handleVote(value) {
    setVoteBusy(true);
    try {
      const res = await topicsApi.voteTopic(id, value);
      setTopic((t) => ({ ...t, vote_score: res.total_score }));
    } catch (err) {
      setError(err);
    } finally {
      setVoteBusy(false);
    }
  }

  async function handleDeleteTopic() {
    if (!confirm("Obrisati temu i sve povezane postove?")) return;
    try {
      await topicsApi.deleteTopic(id);
      navigate("/topics");
    } catch (err) {
      setError(err);
    }
  }

  async function handleReply(e) {
    e.preventDefault();
    if (!replyBody.trim()) return;
    setPosting(true);
    setError(null);
    try {
      await postsApi.createPost({ body: replyBody, topic_id: id });
      setReplyBody("");
      load();
    } catch (err) {
      setError(err);
    } finally {
      setPosting(false);
    }
  }

  if (loading) return <LoadingSpinner />;
  if (error && !topic) return <ErrorMessage error={error} />;
  if (!topic) return null;

  return (
    <div className="page">
      <h1>{topic.title}</h1>
      <p className="topic-meta">
        Autor: Korisnik #{topic.user_id} · {new Date(topic.created_at).toLocaleString("sr-RS")}
      </p>
      <p className="topic-body">{topic.body}</p>

      <div className="vote-row">
        <button type="button" disabled={!isAuthenticated || voteBusy} onClick={() => handleVote(1)}>
          ▲
        </button>
        <span className="vote-score">{topic.vote_score}</span>
        <button type="button" disabled={!isAuthenticated || voteBusy} onClick={() => handleVote(-1)}>
          ▼
        </button>
      </div>

      {canModerateTopic && (
        <div className="card-actions">
          <Link to={`/topics/${id}/edit`}>Izmeni temu</Link>
          <button type="button" onClick={handleDeleteTopic}>
            Obriši temu
          </button>
        </div>
      )}

      <ErrorMessage error={error} />

      <h2>Postovi ({posts.length})</h2>
      {posts.length === 0 && <p>Još nema postova u ovoj temi.</p>}
      {posts.map((post) => (
        <PostThread key={post.id} post={post} onChanged={load} />
      ))}

      {isAuthenticated ? (
        <form onSubmit={handleReply} className="form reply-form">
          <textarea
            placeholder="Napiši odgovor u temi…"
            rows={4}
            value={replyBody}
            onChange={(e) => setReplyBody(e.target.value)}
          />
          <button type="submit" disabled={posting}>
            {posting ? "Slanje…" : "Odgovori"}
          </button>
        </form>
      ) : (
        <p>
          <Link to="/login">Prijavi se</Link> da bi odgovorio na temu.
        </p>
      )}
    </div>
  );
}
