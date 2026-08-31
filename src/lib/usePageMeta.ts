import { useEffect } from 'react'

/**
 * Per-route title and description.
 *
 * This is the SPA prototype's stand-in only. The production build is a
 * WordPress MPA where every one of these is rendered server-side into the
 * document head — which is what actually matters for crawling, because a
 * title written by JS after paint is a title a crawler may never see.
 */
export function usePageMeta(title: string, description?: string) {
  useEffect(() => {
    document.title = title
    if (!description) return
    let tag = document.querySelector<HTMLMetaElement>('meta[name="description"]')
    if (!tag) {
      tag = document.createElement('meta')
      tag.name = 'description'
      document.head.appendChild(tag)
    }
    tag.content = description
  }, [title, description])
}
